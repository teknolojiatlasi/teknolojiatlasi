@foreach ($topics as $topic)
    @include('sinav::admin.topics._topic_row', ['topic' => $topic, 'level' => 0])
@endforeach

