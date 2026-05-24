<tr
    class="{{ $level > 0 ? 'category-child-row' : 'category-root-row' }}"
    data-id="{{ $category->id }}"
    data-name="{{ $category->name }}"
    data-description="{{ $category->description }}"
    data-parent-id="{{ $category->parent_id }}"
    data-sort-order="{{ $category->sort_order }}"
    data-icon="{{ $category->icon }}"
    data-active="{{ $category->is_active ? '1' : '0' }}"
    data-public-url="{{ route('simulation.category', $category) }}"
>
    <td class="category-title-cell">
        <div class="category-tree-item {{ $level > 0 ? 'is-child' : '' }}" style="--category-level: {{ $level }}">
            <span class="category-icon">
                <i class="fa {{ $category->icon ?: ($level > 0 ? 'fa-angle-right' : 'fa-folder-open') }}"></i>
            </span>
            <span class="flex-grow-1">
                <a href="{{ route('simulation.category', $category) }}" class="category-title-text" target="_blank">{{ $category->name }}</a>
                @if ($category->description)
                    <span class="category-meta">{{ $category->description }}</span>
                @endif
            </span>
            <span class="category-level-badge">{{ $level > 0 ? 'Alt dugum' : 'Ana dugum' }}</span>
        </div>
    </td>
    <td>{{ $category->slug }}</td>
    <td>
        @if ($category->is_active)
            <span class="badge bg-success">Aktif</span>
        @else
            <span class="badge bg-secondary">Pasif</span>
        @endif
    </td>
    <td>{{ $category->sort_order }}</td>
    <td class="text-end">
        <button type="button" class="btn btn-sm btn-warning" data-action="edit-category">
            <i class="fa fa-edit"></i> Duzenle
        </button>
        <button type="button" class="btn btn-sm btn-danger" data-action="delete-category">
            <i class="fa fa-trash"></i> Sil
        </button>
    </td>
</tr>

@foreach ($category->childrenRecursive as $child)
    @include('simulation::admin.categories._category_row', ['category' => $child, 'level' => $level + 1])
@endforeach
