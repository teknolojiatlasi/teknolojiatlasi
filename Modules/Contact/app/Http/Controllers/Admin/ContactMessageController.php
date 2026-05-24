<?php

namespace Modules\Contact\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Modules\Contact\Mail\ContactMessageReplyMail;
use Modules\Contact\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->expectsJson()) {
            return $this->datatable($request);
        }

        $unreadCount = ContactMessage::query()->unread()->count();

        return view('contact::admin.messages.index', compact('unreadCount'));
    }

    public function show(Request $request, ContactMessage $message)
    {
        if (! $message->contact_is_read) {
            $message->update([
                'contact_is_read' => true,
                'contact_read_at' => now(),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $message->id,
                'html' => view('contact::admin.messages._show', compact('message'))->render(),
                'row_html' => view('contact::admin.messages._row', compact('message'))->render(),
            ]);
        }

        return view('contact::admin.messages.show', compact('message'));
    }

    public function markRead(Request $request, ContactMessage $message)
    {
        $message->update([
            'contact_is_read' => true,
            'contact_read_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $message->id,
                'html' => view('contact::admin.messages._row', compact('message'))->render(),
            ]);
        }

        return redirect()->route('contact_admin_messages_index');
    }

    public function markUnread(Request $request, ContactMessage $message)
    {
        $message->update([
            'contact_is_read' => false,
            'contact_read_at' => null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $message->id,
                'html' => view('contact::admin.messages._row', compact('message'))->render(),
            ]);
        }

        return redirect()->route('contact_admin_messages_index');
    }

    public function reply(Request $request, ContactMessage $message)
    {
        $data = $request->validate([
            'contact_reply_subject' => ['required', 'string', 'max:255'],
            'contact_reply_message' => ['required', 'string', 'max:10000'],
        ]);

        Mail::to($message->contact_email)->send(new ContactMessageReplyMail(
            message: $message,
            replySubject: $data['contact_reply_subject'],
            replyMessage: $data['contact_reply_message'],
        ));

        $message->update([
            'contact_is_replied' => true,
            'contact_replied_at' => now(),
            'contact_reply_subject' => $data['contact_reply_subject'],
            'contact_reply_message' => $data['contact_reply_message'],
            'contact_replied_by_id' => (int) optional($request->user())->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $message->id,
                'row_html' => view('contact::admin.messages._row', compact('message'))->render(),
                'message' => 'Yanit gonderildi.',
            ]);
        }

        return redirect()->route('contact_admin_messages_index')->with('status', 'Yanit gonderildi.');
    }

    protected function datatable(Request $request): JsonResponse
    {
        $draw = max(0, (int) $request->input('draw', 0));
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length > 0 ? min($length, 100) : 10;
        $search = trim((string) $request->input('search.value', ''));

        $baseQuery = ContactMessage::query();
        $filteredQuery = ContactMessage::query()
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('contact_full_name', 'like', $like)
                        ->orWhere('contact_email', 'like', $like)
                        ->orWhere('contact_subject', 'like', $like)
                        ->orWhere('contact_message', 'like', $like);
                });
            });

        $recordsTotal = (clone $baseQuery)->count();
        $recordsFiltered = (clone $filteredQuery)->count();

        $columns = [
            1 => 'contact_full_name',
            2 => 'contact_email',
            3 => 'contact_subject',
            4 => 'created_at',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 4);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';

        $messages = $filteredQuery
            ->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $messages->map(function (ContactMessage $message): array {
            $statusBadges = [];
            $statusBadges[] = $message->contact_is_read
                ? '<span class="badge bg-secondary">Okundu</span>'
                : '<span class="badge bg-danger">Okunmadi</span>';

            if ($message->contact_is_replied) {
                $statusBadges[] = '<span class="badge bg-success">Yanitlandi</span>';
            }

            return [
                'DT_RowAttr' => [
                    'data-id' => (string) $message->id,
                    'data-is-read' => $message->contact_is_read ? '1' : '0',
                ],
                'status' => implode(' ', $statusBadges),
                'full_name' => e($message->contact_full_name),
                'email' => e($message->contact_email),
                'subject' => e($message->contact_subject),
                'created_at' => e(optional($message->created_at)->format('d.m.Y H:i')),
                'actions' => sprintf(
                    '<div class="text-end d-flex flex-wrap justify-content-end gap-1"><button class="btn btn-sm btn-primary" type="button" data-action="view" data-url="%s"><i class="fa fa-eye"></i> Goruntule</button>%s</div>',
                    e(route('contact_admin_messages_show', $message)),
                    $message->contact_is_read
                        ? sprintf(
                            '<button class="btn btn-sm btn-outline-secondary" type="button" data-action="mark-unread" data-url="%s">Okunmadi</button>',
                            e(route('contact_admin_messages_mark_unread', $message))
                        )
                        : sprintf(
                            '<button class="btn btn-sm btn-outline-success" type="button" data-action="mark-read" data-url="%s">Okundu</button>',
                            e(route('contact_admin_messages_mark_read', $message))
                        )
                ),
            ];
        })->all();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}
