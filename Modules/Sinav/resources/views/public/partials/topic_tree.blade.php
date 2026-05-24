<ul class="list-unstyled mb-0">
    @foreach ($nodes as $node)
        @php
            $topic = $node['topic'];
            $isActive = $activeTopic && $activeTopic->id === $topic->id;
        @endphp
        <li class="mb-1">
            <a
                class="d-inline-block {{ $isActive ? 'fw-bold text-primary' : '' }}"
                href="{{ route('sinav.lessons.show', $lesson) }}?topic_id={{ $topic->id }}"
            >
                {{ $topic->title }}
            </a>
            @if (!empty($node['children']))
                <div class="ms-3 mt-1">
                    @include('sinav::public.partials.topic_tree', ['nodes' => $node['children'], 'lesson' => $lesson, 'activeTopic' => $activeTopic])
                </div>
            @endif
        </li>
    @endforeach
</ul>

