<?php

namespace Modules\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Page\Models\Page;
use Spatie\Permission\Models\Role;

class PageController extends Controller
{
    public function index(): View
    {
        return view('page::index', [
            'pages' => Page::with('roles')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('page::create', [
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug'],
            'content' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $page = Page::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? null,
            'content' => $validated['content'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        $page->roles()->sync($validated['roles']);

        return redirect()
            ->route('page.index')
            ->with('success', 'Sayfa oluşturuldu.');
    }

    public function show(Page $page): View
    {
        abort_unless($page->is_active, 404);

        $user = auth()->user();

        abort_unless(
            $user && ($user->hasAnyRole(['superadmin', 'admin']) || $user->hasAnyRole($page->roles->pluck('name')->all())),
            403
        );

        return view('page::show', [
            'page' => $page->load('roles'),
        ]);
    }

    public function edit(Page $page): View
    {
        return view('page::edit', [
            'page' => $page->load('roles'),
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug,' . $page->id],
            'content' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $page->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? null,
            'content' => $validated['content'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        $page->roles()->sync($validated['roles']);

        return redirect()
            ->route('page.index')
            ->with('success', 'Sayfa güncellendi.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()
            ->route('page.index')
            ->with('success', 'Sayfa silindi.');
    }
}
