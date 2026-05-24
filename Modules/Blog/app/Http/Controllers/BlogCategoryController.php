<?php
namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Blog\Models\BlogCategory;

class BlogCategoryController extends Controller
{
    /* ===== LIST ===== */
    public function index()
    {
        $categories = BlogCategory::with('parent')->orderBy('name')->get();
        return view('blog::categories.index', compact('categories'));
    }

    /* ===== CREATE FORM ===== */
    public function create()
    {
        $parents = BlogCategory::whereNull('parent_id')->get();
        return view('blog::categories.create', compact('parents'));
    }

    /* ===== STORE ===== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:blog_categories,id',
            'name'      => 'required|string|max:255',
        ]);

        $data['slug'] = Str::slug($data['name']);

        BlogCategory::create($data);

        return redirect()
            ->route('blog.categories.index')
            ->with('success', 'Kategori oluşturuldu');
    }

    public function edit(BlogCategory $category)
{
    $parents = BlogCategory::whereNull('parent_id')
        ->where('id', '!=', $category->id)
        ->get();

    return view('blog::categories.edit', compact('category', 'parents'));
}

public function update(Request $request, BlogCategory $category)
{
    $data = $request->validate([
        'parent_id' => 'nullable|exists:blog_categories,id',
        'name' => 'required|string|max:255',
    ]);

    $data['slug'] = \Str::slug($data['name']);

    $category->update($data);

    return redirect()
        ->route('blog.categories.index')
        ->with('success', 'Kategori güncellendi');
}

public function destroy(BlogCategory $category)
{
    BlogCategory::where('parent_id', $category->id)
        ->update(['parent_id' => null]);

    $category->delete();

    return redirect()
        ->route('blog.categories.index')
        ->with('success', 'Kategori silindi');
}


}
