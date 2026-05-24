<?php

namespace Modules\Exam\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Exam\Menu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::parents()->with('children')->orderBy('order')->get();
        return view('exam::admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parents = Menu::parents()->orderBy('name')->get();
        return view('exam::admin.menus.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer|min:0',
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('exam.menus.index')
            ->with('success', 'Menü Başarılı bir Şekilde Oluşturuldu.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $parents = Menu::where('id', '!=', $menu->id)
            ->parents()
            ->orderBy('name')
            ->get();

        return view('exam::admin.menus.edit', compact('menu', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer|min:0',
        ]);

        $menu->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('exam.menus.index')
            ->with('success', 'Menu updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        // Check if menu has children
        if ($menu->hasChildren()) {
            return redirect()->route('exam.menus.index')
                ->with('error', 'Cannot delete menu with children.');
        }

        $menu->delete();

        return redirect()->route('exam.menus.index')
            ->with('success', 'Menu deleted successfully.');
    }

    /**
     * Get menu tree for AJAX requests.
     */
    public function getMenuTree(): JsonResponse
    {
        $menus = Menu::parents()->with('children')->orderBy('order')->get();
        return response()->json($menus);
    }
}
