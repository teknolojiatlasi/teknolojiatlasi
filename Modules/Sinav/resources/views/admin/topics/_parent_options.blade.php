<option value="">(Yok)</option>
@foreach ($allTopics as $t)
    <option value="{{ $t->id }}">{{ $t->title }}</option>
@endforeach

