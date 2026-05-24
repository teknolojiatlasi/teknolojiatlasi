@php
    $isEdit = $post->exists;
    $selectedUserId = (int) old('user_id', $post->user_id);
    $selectedType = old('type', $post->type ?: 'interview');
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-8 col-sm-12">
        <div class="form-group mb-3">
            <label for="post_user_id">Yazar</label>
            <select id="post_user_id" name="user_id" class="form-control" required>
                <option value="">Kullanıcı seçin</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected($selectedUserId === (int) $user->id)>
                        #{{ $user->id }} - {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="post_type">Post tipi</label>
            <select id="post_type" name="type" class="form-control" required>
                @foreach ($typeLabels as $value => $label)
                    <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="post_body">İçerik</label>
            <textarea id="post_body" name="body" class="form-control" rows="10" required>{{ old('body', $post->body) }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label for="post_tags">Etiketler</label>
            <input id="post_tags" type="text" name="tags" class="form-control" value="{{ old('tags', $tagList) }}" placeholder="Örnek: laravel, remote, mülakat">
        </div>

        <div class="form-group mb-3">
            <label for="post_link_url">Link</label>
            <input id="post_link_url" type="url" name="link_url" class="form-control" value="{{ old('link_url', $post->link_url) }}" placeholder="https://...">
        </div>

        <div class="form-group mb-3">
            <label for="post_images">Görseller</label>
            <input id="post_images" type="file" name="images[]" class="form-control" accept="image/*" multiple>
            <small class="form-text text-muted">Toplam en fazla 20 görsel.</small>
        </div>
    </div>

    <div class="col-md-4 col-sm-12">
        @if ($isEdit)
            <div class="x_panel">
                <div class="x_title">
                    <h2>Durum</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <p><strong>ID:</strong> #{{ $post->id }}</p>
                    <p><strong>Yorum:</strong> {{ $post->comments()->count() }}</p>
                    <p><strong>Görsel:</strong> {{ $post->media->count() }}</p>
                    <p><strong>Oluşturulma:</strong> {{ optional($post->created_at)->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}</p>
                    <p class="mb-0">
                        <a href="{{ route('sosial.posts.show', $post) }}" target="_blank" rel="noopener">Postu aç</a>
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

@if ($isEdit && $post->media->isNotEmpty())
    <div class="x_panel">
        <div class="x_title">
            <h2>Mevcut Görseller</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="row">
                @foreach ($post->media as $media)
                    @php
                        $src = $media->url ?: ($media->path ? route('sosial.media.show', $media) : null);
                    @endphp
                    @if ($src)
                        <div class="col-md-4 col-sm-6 mb-3">
                            <label class="d-block">
                                <img src="{{ $src }}" alt="Post görseli" class="img-fluid img-thumbnail mb-2" style="height: 170px; width: 100%; object-fit: cover;">
                                <span class="d-flex align-items-center gap-2">
                                    <input type="checkbox" name="remove_media[]" value="{{ $media->id }}">
                                    <span>Sil</span>
                                </span>
                            </label>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif
