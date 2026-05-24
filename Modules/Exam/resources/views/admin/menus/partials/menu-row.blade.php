<tr>
    <td>{{ str_repeat('— ', $level) }}{{ $menu->name }}</td>
    <td>{{ $menu->parent ? $menu->parent->name : '-' }}</td>
    <td>{{ $menu->order }}</td>
    <td>
        <a href="{{ route('exam.menus.edit', $menu->id) }}" class="btn btn-sm btn-warning">Düzenle</a>
        <form action="{{ route('exam.menus.destroy', $menu->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" 
                    onclick="return confirm('Bu menüyü silmek istediğinizden emin misiniz?')">
                Sil
            </button>
        </form>
    </td>
</tr>
@if($menu->children->count() > 0)
    @foreach($menu->children as $child)
        @include('exam::admin.menus.partials.menu-row', ['menu' => $child, 'level' => $level + 1])
    @endforeach
@endif
