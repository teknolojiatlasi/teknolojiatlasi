@extends('layouts.admin')

@section('title', 'Sosial Yorum Düzenle')

@section('content')
<div class="page-title">
    <div class="title_left">
        <h3>Yorum Düzenle #{{ $comment->id }}</h3>
    </div>
</div>

<div class="clearfix"></div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-md-8 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Yorum Metni</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.sossial.comments.update', $comment) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label for="comment_body">Yorum</label>
                        <textarea id="comment_body" name="body" class="form-control" rows="10" required>{{ old('body', $comment->body) }}</textarea>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-success">Güncelle</button>
                        <a href="{{ route('admin.sossial.comments.index') }}" class="btn btn-secondary">Listeye Dön</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Detay</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <p><strong>Yazar:</strong> {{ $comment->user?->name ?? 'Silinmiş kullanıcı' }}</p>
                <p><strong>E-posta:</strong> {{ $comment->user?->email ?? '-' }}</p>
                <p><strong>Tip:</strong> {{ $comment->parent_id ? 'Yanıt' : 'Ana yorum' }}</p>
                @if($comment->post)
                    <p><strong>Post:</strong>
                        <a href="{{ route('sosial.posts.show', $comment->post) }}" target="_blank" rel="noopener">
                            Postu aç
                        </a>
                    </p>
                @endif
                @if($comment->parent)
                    <p style="white-space: pre-wrap;"><strong>Üst yorum:</strong> {{ \Illuminate\Support\Str::limit($comment->parent->body, 140) }}</p>
                @endif

                <form method="POST" action="{{ route('admin.sossial.comments.destroy', $comment) }}" onsubmit="return confirm('Bu yorum silinsin mi? Alt yanıtlar da silinir.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yorumu Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
