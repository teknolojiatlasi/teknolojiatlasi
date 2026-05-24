@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-group mb-3">
    <label for="tag_name">Etiket adı</label>
    <input id="tag_name" type="text" name="name" class="form-control" value="{{ old('name', $tag->name) }}" required>
</div>

<div class="form-group mb-3">
    <label for="tag_slug">Slug</label>
    <input id="tag_slug" type="text" name="slug" class="form-control" value="{{ old('slug', $tag->slug) }}" placeholder="Boş bırakırsanız otomatik oluşturulur">
</div>
