@php
    $statusBadges = [];
    $statusBadges[] = $message->contact_is_read
        ? '<span class="badge bg-secondary">Okundu</span>'
        : '<span class="badge bg-danger">Okunmadı</span>';
    if ($message->contact_is_replied) {
        $statusBadges[] = '<span class="badge bg-success">Yanıtlandı</span>';
    }
@endphp

<tr data-id="{{ $message->id }}" data-is-read="{{ $message->contact_is_read ? 1 : 0 }}">
    <td>{!! implode(' ', $statusBadges) !!}</td>
    <td>{{ $message->contact_full_name }}</td>
    <td>{{ $message->contact_email }}</td>
    <td>{{ $message->contact_subject }}</td>
    <td>{{ optional($message->created_at)->format('d.m.Y H:i') }}</td>
    <td class="text-end">
        <button
            class="btn btn-sm btn-primary"
            type="button"
            data-action="view"
            data-url="{{ route('contact_admin_messages_show', $message) }}"
        >
            <i class="fa fa-eye"></i> Görüntüle
        </button>
        @if ($message->contact_is_read)
            <button
                class="btn btn-sm btn-outline-secondary"
                type="button"
                data-action="mark-unread"
                data-url="{{ route('contact_admin_messages_mark_unread', $message) }}"
            >
                Okunmadı
            </button>
        @else
            <button
                class="btn btn-sm btn-outline-success"
                type="button"
                data-action="mark-read"
                data-url="{{ route('contact_admin_messages_mark_read', $message) }}"
            >
                Okundu
            </button>
        @endif
    </td>
</tr>

