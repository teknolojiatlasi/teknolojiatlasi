<tr
    class="{{ $level > 0 ? 'topic-child-row' : 'topic-root-row' }}"
    data-id="{{ $topic->id }}"
    data-title="{{ $topic->title }}"
    data-description="{{ $topic->description }}"
    data-parent-id="{{ $topic->parent_id }}"
    data-sort-order="{{ $topic->sort_order }}"
    data-active="{{ $topic->is_active ? '1' : '0' }}"
>
    <td data-col="title" class="topic-title-cell">
        <div class="topic-tree-item {{ $level > 0 ? 'is-child' : '' }}" style="--topic-level: {{ $level }}">
            <span class="topic-icon">
                <i class="fa {{ $level > 0 ? 'fa-angle-right' : 'fa-folder-open' }}"></i>
            </span>
            <span class="flex-grow-1">
                <span class="topic-title-text">{{ $topic->title }}</span>
                @if ($topic->description)
                    <span class="topic-meta">{{ $topic->description }}</span>
                @endif
            </span>
            <span class="topic-level-badge">{{ $level > 0 ? 'Alt konu' : 'Ana konu' }}</span>
        </div>
    </td>
    <td data-col="status">
        @if ($topic->is_active)
            <span class="badge bg-success">Aktif</span>
        @else
            <span class="badge bg-secondary">Pasif</span>
        @endif
    </td>
    <td data-col="order">{{ $topic->sort_order }}</td>
    <td class="text-end">
        <a href="{{ route('sinav.admin.topics.tests.index', $topic) }}" class="btn btn-sm btn-info">
            <i class="fa fa-list"></i> Testler
        </a>
        <button type="button" class="btn btn-sm btn-warning" data-action="edit-topic">
            <i class="fa fa-edit"></i> Düzenle
        </button>
        <button type="button" class="btn btn-sm btn-danger" data-action="delete-topic">
            <i class="fa fa-trash"></i> Sil
        </button>
    </td>
</tr>

@foreach ($topic->children as $child)
    @include('sinav::admin.topics._topic_row', ['topic' => $child, 'level' => $level + 1])
@endforeach
