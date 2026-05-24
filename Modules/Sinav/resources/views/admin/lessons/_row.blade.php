<tr
    data-id="{{ $lesson->id }}"
    data-name="{{ $lesson->name }}"
    data-description="{{ $lesson->description }}"
    data-active="{{ $lesson->is_active ? '1' : '0' }}"
>
    <td>{{ $lesson->name }}</td>
    <td>
        @if ($lesson->is_active)
            <span class="badge bg-success">Aktif</span>
        @else
            <span class="badge bg-secondary">Pasif</span>
        @endif
    </td>
    <td>{{ $lesson->created_at?->format('d.m.Y H:i') }}</td>
    <td class="text-end">
        <a href="{{ route('sinav.admin.lessons.topics.index', $lesson) }}" class="btn btn-sm btn-info">
            <i class="fa fa-sitemap"></i> Konular
        </a>
        <button type="button" class="btn btn-sm btn-warning" data-action="edit-lesson">
            <i class="fa fa-edit"></i> Düzenle
        </button>
        <button type="button" class="btn btn-sm btn-danger" data-action="delete-lesson">
            <i class="fa fa-trash"></i> Sil
        </button>
    </td>
</tr>

