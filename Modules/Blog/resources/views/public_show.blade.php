@extends('layouts.app2')
@section('title', $blog->title . ' | Teknoloji Atlası')
@section('meta')
@php
    $shareDescription = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($blog->content))), 180);
    $shareImage = $blog->cover_image_url ?: asset('favicon-star.svg');
@endphp
<link rel="canonical" href="{{ route('blog.public.show', $blog) }}">
<meta name="description" content="{{ $shareDescription }}">
<meta property="og:locale" content="tr_TR">
<meta property="og:type" content="article">
<meta property="og:site_name" content="Teknoloji Atlası">
<meta property="og:title" content="{{ $blog->title }}">
<meta property="og:description" content="{{ $shareDescription }}">
<meta property="og:url" content="{{ route('blog.public.show', $blog) }}">
<meta property="og:image" content="{{ $shareImage }}">
<meta property="og:image:alt" content="{{ $blog->title }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $blog->title }}">
<meta name="twitter:description" content="{{ $shareDescription }}">
<meta name="twitter:image" content="{{ $shareImage }}">
@endsection
@push('styles')
<style>
    #blogImagesCarousel .carousel-indicators [data-bs-target] {
        width: 2.75rem;
        height: 0.2rem;
        margin-inline: 0.25rem;
        border: 0;
        border-radius: 999px;
        background-color: rgba(255, 255, 255, 0.55);
        opacity: 1;
    }

    #blogImagesCarousel .carousel-indicators .active {
        background-color: #ffffff;
    }

    #blogImagesCarousel .blog-hero-image {
        max-height: 38rem;
        object-fit: cover;
    }

    .blog-content::after,
    #comments::before {
        content: "";
        display: block;
        clear: both;
    }

    #comments {
        clear: both;
    }

    .blog-comment-form .blog-comment-textarea-wrap {
        position: relative;
    }

    .blog-comment-form .blog-comment-textarea {
        min-height: 8.5rem;
        resize: vertical;
        padding-right: 4rem;
    }

    .blog-comment-form .blog-comment-emoji-btn {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        z-index: 2;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .blog-comment-form .js-emoji-panel {
        max-width: 100%;
    }
</style>
@endpush
@section('content')

{{-- <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">

    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('anasayfa') }}">İlan</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('anasayfa') }}">Ana Sayfa</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('blog.public.index') }}">İlan</a></li>
            </ul>
        </div>
    </div>
</nav> --}}

@php
    $carouselImages = collect();
    if ($blog->cover_image_url) {
        $carouselImages->push($blog->cover_image_url);
    }
    foreach ($blog->images as $image) {
        $carouselImages->push($image->image_path);
    }
    $plainContentLength = mb_strlen(trim(strip_tags($blog->content ?? '')));
    $shouldShowInlineAds = $plainContentLength >= 900;
@endphp

<main class="py-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-12 col-lg-8">

        @include('partials.adsense.ad-unit', [
            'slot' => 'blog_top',
            'class' => 'mx-auto',
            'style' => 'max-width: 100%;',
            'insStyle' => 'display:block; text-align:center;',
            'layout' => 'in-article',
            'format' => 'fluid',
            'variant' => 'hero',
            'minHeight' => '120px',
            'label' => null,
        ])

        @if($carouselImages->isNotEmpty())
            <div id="blogImagesCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                @if($carouselImages->count() > 1)
                    <div class="carousel-indicators">
                        @foreach($carouselImages as $index => $path)
                            <button type="button"
                                    data-bs-target="#blogImagesCarousel"
                                    data-bs-slide-to="{{ $index }}"
                                    class="{{ $index === 0 ? 'active' : '' }}"
                                    @if($index === 0) aria-current="true" @endif
                                    aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @endif

                <div class="carousel-inner rounded shadow-sm overflow-hidden">
                    @foreach($carouselImages as $index => $path)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ Str::startsWith($path, ['http://', 'https://', '/']) ? $path : route('blog.media.show', ['path' => $path]) }}"
                                 class="d-block w-100 blog-hero-image"
                                 alt="{{ $blog->title }}"
                                 width="1600"
                                 height="900"
                                 @if($index === 0) fetchpriority="high" @endif>
                        </div>
                    @endforeach
                </div>

                @if($carouselImages->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#blogImagesCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Önceki</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#blogImagesCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Sonraki</span>
                    </button>
                @endif
            </div>
        @endif

        <div class="bg-white rounded shadow-sm p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div class="text-muted small">
                    <span class="me-2">{{ $blog->category->name ?? 'Genel' }}</span>
                    <span>{{ optional($blog->created_at)->format('d.m.Y') }}</span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#comments" class="btn btn-sm px-3 py-2 text-white border-0 shadow-sm" style="background: linear-gradient(135deg, #2563eb, #00fdf6); border-radius: 999px; font-size: 1.3rem; line-height: 1.2;">Yorumları gör</a>
                    <a href="{{ route('blog.public.index') }}" class="btn btn-sm px-3 py-2 text-dark shadow-sm" style="background: linear-gradient(135deg, #fef3c7, #fde68a); border: 1px solid #f59e0b; border-radius: 999px; font-size: 1.3rem; line-height: 1.2;">Tüm ilanlar</a>
                </div>
            </div>

            <h1 class="fw-bold mb-4">{{ $blog->title }}</h1>

            @if($shouldShowInlineAds)
                @include('partials.adsense.ad-unit', [
                    'slot' => 'blog_inline',
                    'style' => 'max-width: 100%;',
                    'insStyle' => 'display:block; text-align:center;',
                    'layout' => 'in-article',
                    'format' => 'fluid',
                    'variant' => 'inline',
                    'minHeight' => '110px',
                    'label' => null,
                ])
            @endif

            <div class="blog-content">
                {!! $blog->content !!}
            </div>
        </div>

        @if($shouldShowInlineAds)
            @include('partials.adsense.ad-unit', [
                'slot' => 'blog_bottom',
                'class' => 'mt-4',
                'style' => 'max-width: 100%;',
                'insStyle' => 'display:block; text-align:center;',
                'layout' => 'in-article',
                'format' => 'fluid',
                'variant' => 'banner',
                'minHeight' => '120px',
                'label' => null,
            ])
        @endif

        <div id="comments" class="mt-4">
            <div class="bg-white rounded shadow-sm p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <h2 class="h5 fw-bold mb-0">Yorumlar ({{ $blog->comments_count ?? 0 }})</h2>
                    <a class="btn btn-sm btn-outline-secondary" href="#comments">Yorum yaz</a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success js-comment-success" role="alert" id="blogCommentGlobalSuccess">
                        {{ session('success') }}
                    </div>
                @else
                    <div class="alert alert-success js-comment-success d-none" role="alert" id="blogCommentGlobalSuccess"></div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger" role="alert" id="blogCommentGlobalError">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="alert alert-danger d-none" role="alert" id="blogCommentGlobalError"></div>
                @endif

                <form method="POST" action="{{ route('blog.comments.store', $blog) }}#comments" class="bg-light border rounded-4 p-3 blog-comment-form js-blog-comment-form" id="blogCommentForm" data-parent-id="">
                    @csrf
                    <input type="hidden" name="parent_id" value="">
                    <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="d-none">
                    @include('partials.bot-protection')

                    <div class="row g-2">
                        <div class="col-12 col-md-4">
                            <label class="form-label small mb-1">Ad</label>
                            <input type="text"
                                   name="author_name"
                                   class="form-control js-comment-author"
                                   maxlength="80"
                                   required
                                   value="{{ old('author_name') }}"
                                   placeholder="Adiniz (zorunlu)">
                            <div class="form-text">E-posta/giris zorunlu degil.</div>
                            <div class="text-danger small mt-1" data-error="author_name"></div>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label small mb-1">Yorum</label>
                            <div class="blog-comment-textarea-wrap">
                                <textarea name="body"
                                          class="form-control js-emoji-target js-comment-body blog-comment-textarea"
                                          rows="3"
                                          maxlength="2000"
                                          required
                                          placeholder="Yorumunuzu yazin...">{{ old('body') }}</textarea>
                                <button type="button" class="btn btn-outline-secondary js-emoji-button blog-comment-emoji-btn" aria-label="Emoji">
                                    :)
                                </button>
                            </div>
                            <div class="border rounded-3 p-2 mt-2 bg-white d-none js-emoji-panel"></div>
                            <div class="form-text d-flex justify-content-between">
                                <span>Emoji ekleyebilirsiniz.</span>
                                <span class="text-muted">Max 2000 karakter</span>
                            </div>
                            <div class="text-danger small mt-1" data-error="body"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-2">
                        <button class="btn btn-primary" type="submit" data-submit-button>Gonder</button>
                    </div>
                </form>

                <div class="mt-4" id="blogCommentList">
                    @forelse(($comments ?? []) as $comment)
                        @include('blog::partials.comment', ['comment' => $comment, 'depth' => 0, 'blog' => $blog])
                    @empty
                        <div class="text-muted js-comment-empty">Henuz yorum yok. Ilk yorumu siz yazin.</div>
                    @endforelse
                </div>
            </div>
        </div>
            </div>
            <div class="col-12 col-lg-4">
                @include('partials.adsense.ad-unit', [
                    'slot' => 'blog_sidebar',
                    'style' => 'max-width: 100%;',
                    'insStyle' => 'display:block; text-align:center;',
                    'format' => 'auto',
                    'variant' => 'sidebar',
                    'minHeight' => '250px',
                    'label' => null,
                ])

                <div class="bg-white rounded shadow-sm p-4">
                    <h2 class="h5 fw-bold mb-3">Son İlanlar</h2>
                    <ul class="list-unstyled mb-0">
                        @forelse($latestBlogs ?? [] as $latestBlog)
                            @php
                                $latestCover = $latestBlog->cover_image_url;
                            @endphp
                            <li class="d-flex gap-3 mb-3">
                                @if($latestCover)
                                    <img src="{{ $latestCover }}" alt="{{ $latestBlog->title }}" class="rounded" style="width: 64px; height: 64px; object-fit: cover;" width="64" height="64">
                                @endif
                                <div>
                                    <div class="text-muted small mb-1">{{ optional($latestBlog->created_at)->format('d.m.Y') }}</div>
                                    <a class="fw-semibold text-decoration-none" href="{{ route('blog.public.show', $latestBlog) }}">
                                        {{ $latestBlog->title }}
                                    </a>
                                </div>
                            </li>
                        @empty
                            <li class="text-muted small">Henüz gösterilecek ilan yok.</li>
                        @endforelse
                    </ul>
                    <div class="mt-3">
                        <a href="{{ route('blog.public.index') }}" class="btn btn-sm btn-outline-primary w-100">
                            Tüm ilanlara git
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    (function () {
        const EMOJIS = [
            '\u{1F600}', '\u{1F601}', '\u{1F602}', '\u{1F923}', '\u{1F60A}', '\u{1F60D}',
            '\u{1F929}', '\u{1F60E}', '\u{1F605}', '\u{1F609}', '\u{1F64C}', '\u{1F44F}',
            '\u{1F64F}', '\u{1F525}', '\u{2764}\u{FE0F}', '\u{1F44D}', '\u{1F44E}', '\u{1F389}',
            '\u{1F621}', '\u{1F622}', '\u{1F62E}', '\u{1F914}', '\u{2705}', '\u{274C}'
        ];
        const STORAGE_KEY = 'blog_comment_author_name';
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const listEl = document.getElementById('blogCommentList');
        const globalErrorEl = document.getElementById('blogCommentGlobalError');
        const globalSuccessEl = document.getElementById('blogCommentGlobalSuccess');
        const commentHeading = document.querySelector('#comments .h5');

        function insertAtCursor(textarea, value) {
            const start = textarea.selectionStart ?? textarea.value.length;
            const end = textarea.selectionEnd ?? textarea.value.length;
            textarea.value = textarea.value.slice(0, start) + value + textarea.value.slice(end);
            const pos = start + value.length;
            textarea.setSelectionRange(pos, pos);
            textarea.focus();
        }

        function fillAuthorInputs() {
            const saved = (localStorage.getItem(STORAGE_KEY) || '').trim();
            if (!saved) return;
            document.querySelectorAll('input.js-comment-author').forEach(function (input) {
                if (!input.value) input.value = saved;
            });
        }

        function clearMessages(form) {
            form?.querySelectorAll('[data-error]').forEach(function (el) {
                el.textContent = '';
            });

            const localError = form?.parentElement?.querySelector('[data-comment-form-error]');
            const localSuccess = form?.parentElement?.querySelector('[data-comment-form-success]');

            if (localError) {
                localError.textContent = '';
                localError.classList.add('d-none');
            }

            if (localSuccess) {
                localSuccess.textContent = '';
                localSuccess.classList.add('d-none');
            }

            if (globalErrorEl) {
                globalErrorEl.textContent = '';
                globalErrorEl.classList.add('d-none');
            }
        }

        function showMessage(form, type, message) {
            const isRoot = form?.id === 'blogCommentForm';
            const target = isRoot
                ? (type === 'success' ? globalSuccessEl : globalErrorEl)
                : form?.parentElement?.querySelector(type === 'success' ? '[data-comment-form-success]' : '[data-comment-form-error]');

            if (!target) return;

            target.textContent = message;
            target.classList.remove('d-none');
        }

        function setFieldErrors(form, errors) {
            Object.entries(errors || {}).forEach(function ([key, messages]) {
                const target = form.querySelector(`[data-error="${key}"]`);
                if (target) target.textContent = (messages || []).join(' ');
            });
        }

        function updateCommentCount(count) {
            const numericCount = Number(count);
            if (!commentHeading || !Number.isFinite(numericCount)) return;
            commentHeading.textContent = `Yorumlar (${numericCount})`;
        }

        function highlightComment(commentId) {
            if (!commentId) return;

            const el = document.getElementById(`comment-${commentId}`);
            if (!el) return;

            el.classList.add('comment-highlight');
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(function () {
                el.classList.remove('comment-highlight');
            }, 2500);
        }

        function hideReplyForm(form) {
            const collapseEl = form.closest('.collapse');
            if (!collapseEl || !window.bootstrap?.Collapse) return;
            window.bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false }).hide();
        }

        function bindDynamicUi(scope) {
            scope.querySelectorAll?.('form.js-blog-comment-form').forEach(bindForm);
            fillAuthorInputs();
        }

        async function handleSubmit(form) {
            if (!csrf || form.dataset.submitting === '1') return;

            clearMessages(form);
            form.dataset.submitting = '1';

            const submitButton = form.querySelector('[data-submit-button]');
            const previousLabel = submitButton?.textContent || 'Gonder';

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Gonderiliyor...';
            }

            try {
                const payload = new FormData(form);
                const response = await fetch(form.action.replace('#comments', ''), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: payload,
                });

                const json = await response.json().catch(function () {
                    return {};
                });

                if (!response.ok) {
                    if (response.status === 422) {
                        setFieldErrors(form, json.errors || {});
                        showMessage(form, 'error', json.message || 'Lutfen alanlari kontrol edin.');
                        return;
                    }

                    showMessage(form, 'error', json.message || 'Islem basarisiz.');
                    return;
                }

                if (json.html) {
                    listEl?.querySelectorAll('.js-comment-empty').forEach(function (el) {
                        el.remove();
                    });

                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = json.html;
                    const element = wrapper.firstElementChild;

                    if (element) {
                        if (json.parent_id) {
                            const repliesEl = document.querySelector(`[data-replies="${json.parent_id}"]`);
                            repliesEl?.prepend(element);
                        } else {
                            listEl?.prepend(element);
                        }

                        bindDynamicUi(element);
                    }
                }

                updateCommentCount(json.comments_count);
                showMessage(form, 'success', json.message || (json.duplicate ? 'Aynı yorum zaten gonderildi.' : 'Yorumunuz eklendi.'));

                if (json.comment_id) {
                    highlightComment(json.comment_id);
                }

                if (!json.duplicate) {
                    const authorValue = form.querySelector('input[name="author_name"]')?.value || '';
                    form.reset();
                    const authorInput = form.querySelector('input[name="author_name"]');
                    if (authorInput) authorInput.value = authorValue;
                    window.botProtection?.reset(form);
                    hideReplyForm(form);
                    fillAuthorInputs();
                }
            } catch (error) {
                showMessage(form, 'error', error?.message || 'Bir hata olustu.');
            } finally {
                form.dataset.submitting = '0';

                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = previousLabel;
                }
            }
        }

        function bindForm(form) {
            if (!form || form.dataset.bound === '1') return;

            form.dataset.bound = '1';
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                handleSubmit(form);
            });
        }

        document.addEventListener('click', function (event) {
            const button = event.target.closest('.js-emoji-button');
            if (!button) return;

            const group = button.closest('.blog-comment-textarea-wrap');
            const textarea = group ? group.querySelector('.js-emoji-target') : null;
            const panel = group ? group.parentElement.querySelector('.js-emoji-panel') : null;
            if (!textarea || !panel) return;

            panel.classList.toggle('d-none');
            if (panel.dataset.ready === '1') return;

            panel.dataset.ready = '1';
            panel.classList.add('d-flex', 'flex-wrap', 'gap-2');

            EMOJIS.forEach(function (emoji) {
                const emojiButton = document.createElement('button');
                emojiButton.type = 'button';
                emojiButton.className = 'btn btn-sm btn-outline-secondary';
                emojiButton.textContent = emoji;
                emojiButton.addEventListener('click', function () {
                    insertAtCursor(textarea, emoji);
                });
                panel.appendChild(emojiButton);
            });
        });

        document.addEventListener('input', function (event) {
            const input = event.target.closest('input.js-comment-author');
            if (!input) return;

            const name = (input.value || '').trim();
            if (!name) return;

            localStorage.setItem(STORAGE_KEY, name);
        });

        fillAuthorInputs();
        document.querySelectorAll('form.js-blog-comment-form').forEach(bindForm);

        if (document.querySelector('.js-comment-success') && listEl) {
            listEl.querySelectorAll('textarea.js-comment-body').forEach(function (textarea) {
                if (!textarea.closest('.collapse')) textarea.value = '';
            });
        }

        const hash = (location.hash || '').replace('#', '');
        if (hash && hash.startsWith('comment-')) {
            highlightComment(hash.replace('comment-', ''));
        }
    }());
</script>
@endsection
