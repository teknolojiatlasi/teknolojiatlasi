@csrf

<div class="mb-3">
    <label class="form-label">Başlık</label>
    <input
        type="text"
        name="title"
        class="form-control"
        value="{{ old('title', $page->title ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label class="form-label">Slug</label>
    <input
        type="text"
        name="slug"
        class="form-control"
        value="{{ old('slug', $page->slug ?? '') }}"
        placeholder="Boş bırakırsan otomatik oluşturulur"
    >
</div>

<div class="mb-3">
    <label class="form-label">İçerik</label>
    <textarea
        name="content"
        class="form-control"
        rows="10"
    >{{ old('content', $page->content ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Sayfayı görebilecek roller</label>
    <select name="roles[]" class="form-control" multiple required size="6">
        @php
            $selectedRoles = collect(old('roles', isset($page) ? $page->roles->pluck('id')->all() : []))->map(fn ($roleId) => (int) $roleId)->all();
        @endphp

        @foreach($roles as $role)
            <option value="{{ $role->id }}" @selected(in_array($role->id, $selectedRoles, true))>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">Birden fazla rol seçebilirsiniz.</small>
</div>

<div class="form-check mb-3">
    <input
        class="form-check-input"
        type="checkbox"
        name="is_active"
        value="1"
        id="is_active"
        @checked(old('is_active', $page->is_active ?? true))
    >
    <label class="form-check-label" for="is_active">
        Aktif
    </label>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
