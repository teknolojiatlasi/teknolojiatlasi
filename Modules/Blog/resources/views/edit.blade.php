@extends('app::layouts.admin')

@section('title', 'Blog Duzenle')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.min.css">
<style>
    .jodit-container {
        border: 1px solid #dbe3ee !important;
        border-radius: 18px !important;
        overflow: hidden;
    }
    .jodit-container .jodit-toolbar__box:not(:empty) {
        border-bottom: 1px solid #dbe3ee !important;
        background: #f8fafc;
    }
    .jodit-container .jodit-workplace {
        min-height: 420px;
    }
    .jodit-container .jodit-wysiwyg,
    .jodit-container .jodit-source {
        font-size: 1rem;
        line-height: 1.8;
    }
</style>
@endpush

@section('content')
<form method="POST"
      action="{{ route('blog.update', $blog) }}"
      enctype="multipart/form-data">

    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="blog_category_id" class="form-control" required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ $blog->blog_category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @foreach($category->children as $child)
                    <option value="{{ $child->id }}"
                        {{ $blog->blog_category_id == $child->id ? 'selected' : '' }}>
                        - {{ $child->name }}
                    </option>
                @endforeach
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Baslik</label>
        <input type="text"
               name="title"
               class="form-control"
               value="{{ old('title', $blog->title) }}"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Icerik</label>
        <textarea name="content"
                  id="editor"
                  required>{{ old('content', $blog->content) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Kapak Resmi</label>
        <input type="file" name="cover_image" class="form-control">

        @if($blog->cover_image)
            <div class="mt-2">
                <img src="{{ route('blog.media.show', ['path' => $blog->cover_image]) }}"
                     style="max-height:150px;"
                     class="img-thumbnail">
            </div>
        @endif
    </div>

    <div class="form-check mb-3">
        <input type="hidden" name="share_on_social" value="0">
        <input type="checkbox"
               name="share_on_social"
               id="share_on_social"
               value="1"
               class="form-check-input"
               @checked(old('share_on_social', $blog->social_post_exists ? '1' : '0') === '1')>
        <label class="form-check-label" for="share_on_social">
            Sosyal kisimda paylasilmasini onayliyor musunuz?
        </label>
    </div>

    <div class="mb-3">
        <label class="form-label">Galeriye Yeni Resimler Ekle</label>
        <input type="file"
               name="images[]"
               class="form-control"
               multiple>
        <small class="text-muted">
            Buradan eklenen resimler mevcut galeriye eklenir.
        </small>
    </div>

    <button type="submit" class="btn btn-success">
        Guncelle
    </button>

    <a href="{{ route('blog.index') }}" class="btn btn-secondary">
        Iptal
    </a>
</form>

@if($blog->images->count())
    <hr>
    <h5>Galeri Resimleri</h5>

    <div class="row">
        @foreach($blog->images as $image)
            <div class="col-md-3 mb-3 text-center">
                <img src="{{ route('blog.media.show', ['path' => $image->image_path]) }}"
                     class="img-fluid rounded mb-2"
                     style="max-height:150px;">

                <form method="POST"
                      action="{{ route('blog.image.delete', $image) }}"
                      onsubmit="return confirm('Bu resmi silmek istiyor musunuz?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-sm btn-danger">
                        Sil
                    </button>
                </form>
            </div>
        @endforeach
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.min.js"></script>
<script>
const blogUploadHeaders = {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    'X-Requested-With': 'XMLHttpRequest',
    Accept: 'application/json'
};

function createBlogEditor(selector) {
    const editor = Jodit.make(selector, {
        height: 420,
        minHeight: 420,
        askBeforePasteHTML: false,
        askBeforePasteFromWord: false,
        defaultActionOnPaste: 'insert_as_html',
        buttons: [
            'source', '|',
            'bold', 'italic', 'underline', 'strikethrough', '|',
            'brush', 'paragraph', 'fontsize', '|',
            'ul', 'ol', 'outdent', 'indent', '|',
            'align', 'superscript', 'subscript', '|',
            'table', 'link', 'image', 'video', '|',
            'undo', 'redo', '|',
            'hr', 'eraser', 'fullsize'
        ],
        uploader: {
            insertImageAsBase64URI: false,
            imagesExtensions: ['jpg', 'png', 'jpeg', 'gif', 'webp'],
            customUploadFunction: async function (requestData, showProgress) {
                const formData = new FormData();
                const uploadedFiles = requestData instanceof FormData
                    ? requestData.getAll('files[0]').concat(requestData.getAll('files[]'))
                    : [];

                const file = uploadedFiles.find(Boolean);
                if (!file) {
                    throw new Error('Resim secilmedi.');
                }

                formData.append('upload', file);

                const response = await fetch("{{ route('blog.ckeditor.upload') }}", {
                    method: 'POST',
                    headers: blogUploadHeaders,
                    body: formData,
                    credentials: 'same-origin'
                });

                showProgress(100);

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(data?.error?.message || data?.message || 'Resim yuklenemedi.');
                }

                return data;
            },
            isSuccess: function (resp) {
                return !!resp?.url;
            },
            getMessage: function (resp) {
                return resp?.message || '';
            },
            process: function (resp) {
                return {
                    files: resp?.url ? [resp.url] : [],
                    path: '',
                    baseurl: '',
                    error: resp?.url ? 0 : 1,
                    msg: resp?.message || ''
                };
            },
            defaultHandlerSuccess: function (data) {
                if (data.files && data.files.length) {
                    data.files.forEach((fileUrl) => {
                        this.s.insertImage(fileUrl);
                    });
                }
            },
            defaultHandlerError: function (error) {
                this.j.message.error(error?.message || error || 'Resim yuklenemedi.');
            }
        },
        events: {
            change: function (newValue) {
                const source = document.querySelector(selector);
                if (source) {
                    source.value = newValue;
                }
            }
        }
    });

    return editor;
}

const blogEditorInstance = createBlogEditor('#editor');

const form = document.querySelector('form[action="{{ route('blog.update', $blog) }}"]');
if (form) {
    form.addEventListener('submit', function () {
        const source = document.querySelector('#editor');
        if (source) {
            source.value = blogEditorInstance.value;
        }
    });
}
</script>
@endsection
