<?php

namespace Modules\Blog\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Blog\Models\BlogComment;

class BlogCommentController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->expectsJson()) {
            return $this->datatable($request);
        }

        return view('blog::admin.comments.index');
    }

    protected function datatable(Request $request): JsonResponse
    {
        $draw = max(0, (int) $request->input('draw', 0));
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length > 0 ? min($length, 100) : 10;
        $search = trim((string) $request->input('search.value', ''));

        $baseQuery = BlogComment::query();
        $filteredQuery = BlogComment::query()
            ->with(['blog:id,title,slug'])
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($query) use ($like) {
                    $query->where('author_name', 'like', $like)
                        ->orWhere('body', 'like', $like)
                        ->orWhereHas('blog', function ($query) use ($like) {
                            $query->where('title', 'like', $like)
                                ->orWhere('slug', 'like', $like);
                        });
                });
            });

        $recordsTotal = (clone $baseQuery)->count();
        $recordsFiltered = (clone $filteredQuery)->count();

        $columns = [
            0 => 'id',
            2 => 'author_name',
            5 => 'created_at',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 5);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';

        $comments = $filteredQuery
            ->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $comments->map(function (BlogComment $comment): array {
            $blogHtml = '<span class="text-muted">Silinmis yazi</span>';

            if ($comment->blog) {
                $blogUrl = route('blog.public.show', $comment->blog) . '#comments';
                $blogHtml = sprintf(
                    '<div class="fw-semibold">%s</div><a class="small text-muted" target="_blank" rel="noopener" href="%s">/blog/%s</a>',
                    e($comment->blog->title),
                    e($blogUrl),
                    e($comment->blog->slug)
                );
            }

            $typeHtml = $comment->parent_id
                ? '<span class="badge bg-secondary">Yanit</span>'
                : '<span class="badge bg-info">Yorum</span>';

            $deleteForm = sprintf(
                '<form action="%s" method="POST" onsubmit="return confirm(\'Bu yorumu silmek istiyor musunuz? (Alt yanitlar da silinir)\');">%s%s<button class="btn btn-sm btn-danger" type="submit"><i class="fa fa-trash"></i> Sil</button></form>',
                e(route('blog.comments.destroy', $comment)),
                csrf_field(),
                method_field('DELETE')
            );

            return [
                'id' => '<span class="text-muted">#' . e((string) $comment->id) . '</span>',
                'blog' => $blogHtml,
                'author_name' => e($comment->author_name),
                'body' => nl2br(e(\Illuminate\Support\Str::limit($comment->body, 220))),
                'type' => $typeHtml,
                'created_at' => e(optional($comment->created_at)->timezone('Europe/Istanbul')->format('d.m.Y H:i')),
                'actions' => $deleteForm,
            ];
        })->all();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function destroy(BlogComment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Yorum silindi.');
    }
}
