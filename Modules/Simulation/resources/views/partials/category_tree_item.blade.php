<li class="simulation-tree-item">
    <a
        href="{{ route('simulation.category', $node) }}"
        class="simulation-tree-link {{ $currentCategoryId === $node->id ? 'is-active' : '' }}"
        style="--level: {{ $level }}"
    >
        <span class="simulation-tree-link__name">{{ $node->name }}</span>
        @if ($node->childrenRecursive->isNotEmpty())
            <span class="simulation-tree-link__meta">{{ $node->childrenRecursive->count() }}</span>
        @endif
    </a>

    @if ($node->childrenRecursive->isNotEmpty())
        <ul class="mt-2">
            @foreach ($node->childrenRecursive as $child)
                @include('simulation::partials.category_tree_item', [
                    'node' => $child,
                    'level' => $level + 1,
                    'currentCategoryId' => $currentCategoryId,
                ])
            @endforeach
        </ul>
    @endif
</li>
