<?php

namespace Modules\Exam\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Exam\Exam;
use Modules\Exam\Menu;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = Exam::with('menu')->orderBy('created_at', 'desc')->get();
        return view('exam::admin.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menus = Menu::orderBy('name')->get();
        return view('exam::admin.exams.create', compact('menus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'menu_id' => 'required|exists:menus,id',
            'is_active' => 'required|boolean',
        ]);

        $exam = Exam::create([
            'title' => $request->title,
            'description' => $request->description,
            'menu_id' => $request->menu_id,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('exam.exams.index')
            ->with('success', 'Sınav başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $exam->load('questions');
        return view('exam::admin.exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        $menus = Menu::orderBy('name')->get();
        return view('exam::admin.exams.edit', compact('exam', 'menus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'menu_id' => 'required|exists:menus,id',
            'is_active' => 'required|boolean',
        ]);

        $exam->update([
            'title' => $request->title,
            'description' => $request->description,
            'menu_id' => $request->menu_id,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('exam.exams.index')
            ->with('success', 'Sınav başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('exam.exams.index')
            ->with('success', 'Sınav başarıyla silindi.');
    }
}
