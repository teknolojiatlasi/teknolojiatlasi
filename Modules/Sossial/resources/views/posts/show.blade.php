@php
    $rootCommentCount = is_countable($rootComments ?? null) ? count($rootComments) : 0;
    $isOwner = auth()->check() && auth()->user()?->is($post->user);
    $postTypeLabels = [
        'interview' => 'Mülakat Deneyimi',
        'advice' => 'Kariyer Tavsiyesi',
        'company' => 'Şirket Deneyimi',
        'ilan' => 'Yazı Paylaşımı',
    ];
@endphp

<x-sossial::layouts.master :title="$post->blog?->title ?? 'Paylaşım'">
    <section class="sosial-page-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-lg-8">
                <span class="sosial-kicker"><i class="fa fa-commenting-o"></i> Paylaşım Detayı</span>
                <h1 class="sosial-hero-title">{{ $post->blog?->title ?? 'Tek paylaşım, tam etkileşim görünümü.' }}</h1>
                <p class="sosial-hero-copy">
                    {{ $post->type === 'ilan' ? 'Yazı kapak görseli, başlık ve detay bağlantısı sosyal akış içinde paylaşılıyor.' : 'İçeriği, yorumları ve kullanıcı bağlantılarını tek ekranda yönet.' }}
                </p>
            </div>
            <div class="col-12 col-lg-4">
                <div class="sosial-hero-metrics">
                    <div class="sosial-hero-metric">
                        <span class="sosial-hero-metric-value">{{ (int) ($post->comments_count ?? $rootCommentCount) }}</span>
                        <span class="sosial-hero-metric-label">Yorum</span>
                    </div>
                    <div class="sosial-hero-metric">
                        <span class="sosial-hero-metric-value">{{ $post->created_at?->diffForHumans() }}</span>
                        <span class="sosial-hero-metric-label">Yayın zamanı</span>
                    </div>
                    <div class="sosial-hero-metric">
                        <a class="btn sosial-btn-secondary w-100" href="{{ route('sosial.profile.show', $post->user) }}">Profili Gör</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (session('status'))
        <div class="alert alert-success rounded-4 mb-4">{{ session('status') }}</div>
    @endif

    <div class="row g-4 align-items-start">
        <div class="col-12 col-xl-8">
            @include('sossial::partials.post-card', ['post' => $post, 'showActions' => true, 'showCommentsPreview' => false])

            <div class="sosial-surface sosial-panel mt-4" id="comments">
                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                    <div>
                        <h2 class="sosial-panel-title mb-1">Yorumlar</h2>
                        <p class="sosial-panel-copy">Paylaşıma verilen yanıtlar ve tartışma akışı.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="sosial-chip sosial-chip-soft">{{ $rootCommentCount }} kök yorum</span>
                        @if ($rootCommentCount > 0)
                            <button class="btn btn-sm btn-light rounded-pill px-3" type="button" id="sosialExpandAllComments">Tümünü aç</button>
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" type="button" id="sosialCollapseAllComments">Tümünü kapat</button>
                        @endif
                    </div>
                </div>

                @guest
                    <div class="sosial-empty border rounded-4">
                        <div class="sosial-empty-icon"><i class="fa fa-sign-in"></i></div>
                        <div class="fw-semibold mb-1">Yorum için giriş yap</div>
                        <p class="mb-0">Tartışmaya katılmak için hesabınla oturum aç.</p>
                    </div>
                @else
                    <div class="alert alert-danger d-none rounded-4" id="sosialCommentError"></div>
                    <form id="sosialCommentForm" class="mb-4">
                        @csrf
                        @include('partials.bot-protection')
                        <div class="row g-2">
                            <div class="col-12 col-md">
                                <input class="form-control sosial-form-control" name="body" placeholder="Yorum yaz..." required>
                            </div>
                            <div class="col-12 col-md-auto">
                                <button class="btn sosial-btn-primary w-100" type="submit" id="sosialCommentSubmitBtn">Gönder</button>
                            </div>
                        </div>
                        <div class="text-danger small mt-1" data-error="body"></div>
                    </form>
                @endguest

                <div id="sosialCommentList" class="d-grid gap-3">
                    @forelse ($rootComments as $comment)
                        @include('sossial::partials.comment-node', ['comment' => $comment, 'children' => $children])
                    @empty
                        <div class="sosial-empty border rounded-4">
                            <div class="sosial-empty-icon"><i class="fa fa-comments-o"></i></div>
                            <div class="fw-semibold mb-1">Henüz yorum yok</div>
                            <p class="mb-0">İlk yorumu bırakarak konuşmayı başlat.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="sosial-surface sosial-panel">
                <div class="d-flex align-items-center gap-3">
                    <img
                        src="{{ $post->user?->avatarUrl() }}"
                        alt="{{ $post->user->name ?? 'Kullanıcı' }}"
                        class="sosial-avatar"
                    >
                    <div>
                        <div class="fw-bold">{{ $post->user->name ?? 'Kullanıcı' }}</div>
                        <div class="text-muted small">{{ $post->created_at?->diffForHumans() }}</div>
                    </div>
                </div>

                <div class="mt-4 d-grid gap-3">
                    <div>
                        <div class="text-muted small mb-1">Paylaşım türü</div>
                        <span class="sosial-chip">{{ $postTypeLabels[$post->type] ?? ucfirst((string) $post->type) }}</span>
                    </div>
                    <div>
                        <div class="text-muted small mb-1">Bağlantı</div>
                        @if ($post->link_url)
                            <a class="btn sosial-btn-ghost w-100" href="{{ $post->link_url }}" target="_blank" rel="noopener">Yazı detayına git</a>
                        @else
                            <span class="text-muted small">Bağlantı yok.</span>
                        @endif
                    </div>
                    @if ($isOwner)
                        <div>
                            <div class="text-muted small mb-1">Yönetim</div>
                            <a class="btn btn-outline-primary rounded-pill w-100" href="{{ route('sosial.posts.edit', $post) }}">Paylaşımı Düzenle</a>
                        </div>
                    @else
                        <div>
                            @include('sossial::partials.follow-button', ['profileUser' => $post->user, 'isFollowing' => $isFollowing])
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                @include('sossial::partials.tag-sidebar', [
                    'recentTags' => $recentTags,
                    'popularTags' => $popularTags,
                ])
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const formEl = document.getElementById('sosialCommentForm');
                if (formEl && formEl.dataset.bound === '1') return;
                if (formEl) formEl.dataset.bound = '1';

                const submitBtn = document.getElementById('sosialCommentSubmitBtn');
                const listEl = document.getElementById('sosialCommentList');
                const errorEl = document.getElementById('sosialCommentError');
                const expandAllBtn = document.getElementById('sosialExpandAllComments');
                const collapseAllBtn = document.getElementById('sosialCollapseAllComments');
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                let submitting = false;

                function getCollapseInstance(el) {
                    return bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                }

                function showCollapseById(id) {
                    const el = document.getElementById(id);
                    if (el) getCollapseInstance(el).show();
                }

                function hideCollapseById(id) {
                    const el = document.getElementById(id);
                    if (el) getCollapseInstance(el).hide();
                }

                function syncThreadToggle(button, isExpanded) {
                    if (!button) return;
                    button.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
                    button.textContent = isExpanded ? 'Yanıtları gizle' : 'Yanıtları göster';
                }

                function bindThreadToggleState(scope = document) {
                    scope.querySelectorAll('.sosial-thread-toggle').forEach(button => {
                        const targetSelector = button.getAttribute('data-bs-target');
                        if (!targetSelector) return;
                        const target = document.querySelector(targetSelector);
                        if (!target || target.dataset.threadBound === '1') return;

                        target.dataset.threadBound = '1';
                        syncThreadToggle(button, target.classList.contains('show'));
                        target.addEventListener('shown.bs.collapse', () => syncThreadToggle(button, true));
                        target.addEventListener('hidden.bs.collapse', () => syncThreadToggle(button, false));
                    });
                }

                function bindCommentToggleState(scope = document) {
                    scope.querySelectorAll('.sosial-comment-toggle').forEach(button => {
                        const targetSelector = button.getAttribute('data-bs-target');
                        if (!targetSelector) return;
                        const target = document.querySelector(targetSelector);
                        if (!target || target.dataset.cardBound === '1') return;

                        target.dataset.cardBound = '1';
                        const card = button.closest('.sosial-comment-card');
                        if (card && target.classList.contains('show')) {
                            card.classList.add('is-expanded');
                        }

                        target.addEventListener('shown.bs.collapse', () => card?.classList.add('is-expanded'));
                        target.addEventListener('hidden.bs.collapse', () => card?.classList.remove('is-expanded'));
                    });
                }

                function bindCommentUi(scope = document) {
                    bindThreadToggleState(scope);
                    bindCommentToggleState(scope);
                }

                function showAllComments() {
                    document.querySelectorAll('[id^="commentBody-"], [id^="commentThread-"]').forEach(el => {
                        getCollapseInstance(el).show();
                    });
                }

                function hideAllComments() {
                    document.querySelectorAll('[id^="commentThread-"], [id^="commentBody-"]').forEach(el => {
                        getCollapseInstance(el).hide();
                    });
                }

                function clearError() {
                    if (!errorEl || !formEl) return;
                    errorEl.classList.add('d-none');
                    errorEl.textContent = '';
                    formEl.querySelectorAll('[data-error]').forEach(el => el.textContent = '');
                }

                function setFieldErrors(errors) {
                    if (!formEl) return;
                    Object.entries(errors || {}).forEach(([key, messages]) => {
                        const el = formEl.querySelector(`[data-error="${key}"]`);
                        if (el) el.textContent = (messages || []).join(' ');
                    });
                }

                async function submitForm(e) {
                    e.preventDefault();
                    if (!formEl || !submitBtn || !csrf || submitting) return;
                    clearError();
                    submitting = true;
                    submitBtn.disabled = true;

                    try {
                        const res = await fetch(@json(route('sosial.comments.store', ['post' => $post->id])), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                            body: new FormData(formEl),
                        });

                        const json = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            if (res.status === 422) {
                                setFieldErrors(json.errors);
                                return;
                            }
                            throw new Error(json.message || 'İşlem başarısız');
                        }

                        if (json.html && listEl) {
                            listEl.querySelectorAll('.sosial-empty').forEach(el => el.remove());
                            const wrapper = document.createElement('div');
                            wrapper.innerHTML = json.html;
                            const element = wrapper.firstElementChild;
                            if (element) {
                                listEl.prepend(element);
                                bindCommentUi(element);
                                const bodyCollapse = element.querySelector('[id^="commentBody-"]');
                                if (bodyCollapse) getCollapseInstance(bodyCollapse).show();
                            }
                        }

                        formEl.reset();
                        window.botProtection?.reset(formEl);
                    } catch (err) {
                        if (errorEl) {
                            errorEl.textContent = err.message || 'Bir hata oluştu.';
                            errorEl.classList.remove('d-none');
                        }
                    } finally {
                        submitting = false;
                        submitBtn.disabled = false;
                    }
                }

                if (formEl) {
                    formEl.addEventListener('submit', submitForm);
                }

                window.__sosialReply = async function (parentId, inputEl) {
                    if (!csrf) return;
                    const body = (inputEl.value || '').trim();
                    if (!body) return;

                    const urlTemplate = @json(route('sosial.comments.reply', ['comment' => 0]));
                    const url = urlTemplate.replace('/comments/0/', `/comments/${parentId}/`);

                    const params = new URLSearchParams({ body });
                    const payload = new FormData();
                    window.botProtection?.appendToFormData(formEl, payload);

                    ['_trap', '_started_at', 'cf-turnstile-response'].forEach((key) => {
                        const value = payload.get(key);
                        if (typeof value === 'string') {
                            params.append(key, value);
                        }
                    });

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: params,
                    });

                    const json = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(json.message || 'İşlem başarısız');

                    const target = document.querySelector(`[data-replies="${parentId}"]`);
                    if (target && json.html) {
                        const w = document.createElement('div');
                        w.innerHTML = json.html;
                        const element = w.firstElementChild;
                        if (element) {
                            target.prepend(element);
                            bindCommentUi(element);
                        }
                    }
                    const parentCard = document.getElementById(`comment-${parentId}`);
                    parentCard?.querySelector('.sosial-thread-toggle')?.classList.remove('d-none');
                    showCollapseById(`commentBody-${parentId}`);
                    showCollapseById(`commentThread-${parentId}`);
                    hideCollapseById(`replyBox-${parentId}`);
                    inputEl.value = '';
                    window.botProtection?.reset(formEl);
                };

                expandAllBtn?.addEventListener('click', showAllComments);
                collapseAllBtn?.addEventListener('click', hideAllComments);
                bindCommentUi();
            })();
        </script>
        @auth
            @include('sossial::partials.follow-script')
        @endauth
    @endpush
</x-sossial::layouts.master>
