<?php

namespace Modules\Sossial\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Sossial\Models\Comment;

class CommentController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->expectsJson()) {
            return $this->datatable($request);
        }

        return view('sossial::admin.comments.index');
    }

    protected function datatable(Request $request): JsonResponse
    {
        $draw = max(0, (int) $request->input('draw', 0));
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length > 0 ? min($length, 100) : 10;
        $search = trim((string) $request->input('search.value', ''));

        $baseQuery = Comment::query();
        $filteredQuery = Comment::query()
            ->with([
                'user:id,name,email',
                'post:id,body',
                'parent:id,body',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('body', 'like', $like)
                        ->orWhereHas('user', function ($query) use ($like) {
                            $query->where('name', 'like', $like)
                                ->orWhere('email', 'like', $like);
                        })
                        ->orWhereHas('post', function ($query) use ($like) {
                            $query->where('body', 'like', $like);
                        });
                });
            });

        $recordsTotal = (clone $baseQuery)->count();
        $recordsFiltered = (clone $filteredQuery)->count();

        $columns = [
            0 => 'id',
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

        $data = $comments->map(function (Comment $comment): array {
            $userHtml = '<div>Silinmis kullanici</div><small class="text-muted">-</small>';
            if ($comment->user) {
                $userHtml = sprintf(
                    '<div>%s</div><small class="text-muted">%s</small>',
                    e($comment->user->name),
                    e($comment->user->email ?? '-')
                );
            }

            $typeHtml = $comment->parent_id
                ? '<span class="label label-default">Yanit</span>'
                : '<span class="label label-info">Ana yorum</span>';

            $postHtml = '<span class="text-muted">Silinmis post</span>';
            if ($comment->post) {
                $postHtml = sprintf(
                    '<a href="%s" target="_blank" rel="noopener">%s</a>',
                    e(route('sosial.posts.show', $comment->post)),
                    e(\Illuminate\Support\Str::limit($comment->post->body, 110))
                );
            }

            $actions = sprintf(
                '<div class="d-flex gap-1"><a href="%s" class="btn btn-xs btn-primary">Duzenle</a><form method="POST" action="%s" onsubmit="return confirm(\'Bu yorum silinsin mi? Alt yanitlar da silinir.\');">%s%s<button type="submit" class="btn btn-xs btn-danger">Sil</button></form></div>',
                e(route('admin.sossial.comments.edit', $comment)),
                e(route('admin.sossial.comments.destroy', $comment)),
                csrf_field(),
                method_field('DELETE')
            );

            return [
                'id' => '#' . e((string) $comment->id),
                'user' => $userHtml,
                'type' => $typeHtml,
                'post' => $postHtml,
                'body' => nl2br(e(\Illuminate\Support\Str::limit($comment->body, 180))),
                'created_at' => e(optional($comment->created_at)->timezone('Europe/Istanbul')->format('d.m.Y H:i')),
                'actions' => $actions,
            ];
        })->all();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function edit(Comment $comment): View
    {
        $comment->load([
            'user:id,name,email',
            'post:id,body',
            'parent:id,body',
        ]);

        return view('sossial::admin.comments.edit', [
            'comment' => $comment,
        ]);
    }

    public function update(Request $request, Comment $comment): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $body = trim((string) $data['body']);
        abort_if($body === '', 422, 'Yorum bos olamaz.');

        $comment->update([
            'body' => $body,
        ]);

        return redirect()
            ->route('admin.sossial.comments.edit', $comment)
            ->with('success', 'Yorum guncellendi.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return redirect()
            ->route('admin.sossial.comments.index')
            ->with('success', 'Yorum silindi.');
    }
}
