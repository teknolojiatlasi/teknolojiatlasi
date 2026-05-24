<x-sossial::layouts.master :title="'Mesajlar'">
    <style>
        .sosial-chat-shell {
            display: grid;
            gap: 1rem;
            grid-template-columns: minmax(260px, 340px) minmax(0, 1fr);
        }

        .sosial-chat-list,
        .sosial-chat-panel {
            min-height: 620px;
        }

        .sosial-chat-contact {
            align-items: center;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 1rem;
            color: var(--sosial-dark);
            display: flex;
            gap: 0.85rem;
            padding: 0.85rem;
            transition: background 0.15s ease, border-color 0.15s ease;
        }

        .sosial-chat-contact:hover,
        .sosial-chat-contact.active {
            background: #eff6ff;
            border-color: rgba(37, 99, 235, 0.28);
        }

        .sosial-chat-contact img,
        .sosial-chat-header img {
            border-radius: 50%;
            height: 44px;
            object-fit: cover;
            width: 44px;
        }

        .sosial-chat-unread {
            align-items: center;
            background: #2563eb;
            border-radius: 999px;
            color: #fff;
            display: inline-flex;
            font-size: 12px;
            font-weight: 700;
            height: 22px;
            justify-content: center;
            min-width: 22px;
            padding: 0 6px;
        }

        .sosial-chat-header {
            align-items: center;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            display: flex;
            gap: 0.85rem;
            margin: -1.2rem -1.2rem 0;
            padding: 1.1rem 1.2rem;
        }

        .sosial-chat-messages {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-height: 430px;
            overflow-y: auto;
            padding: 1.2rem 0;
        }

        .sosial-chat-bubble {
            border-radius: 1rem;
            max-width: min(72%, 620px);
            padding: 0.8rem 0.95rem;
        }

        .sosial-chat-bubble.is-own {
            align-self: flex-end;
            background: #2563eb;
            color: #fff;
        }

        .sosial-chat-bubble.is-other {
            align-self: flex-start;
            background: #f1f5f9;
            color: var(--sosial-dark);
        }

        .sosial-chat-time {
            display: block;
            font-size: 11px;
            margin-top: 0.4rem;
            opacity: 0.72;
        }

        .sosial-chat-form {
            border-top: 1px solid rgba(15, 23, 42, 0.08);
            margin: 0 -1.2rem -1.2rem;
            padding: 1rem 1.2rem 1.2rem;
        }

        @media (max-width: 991.98px) {
            .sosial-chat-shell {
                grid-template-columns: 1fr;
            }

            .sosial-chat-list,
            .sosial-chat-panel {
                min-height: auto;
            }
        }
    </style>

    <section class="sosial-page-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-lg-8">
                <span class="sosial-kicker"><i class="fa fa-envelope"></i> Mesajlar</span>
                <h1 class="sosial-hero-title">Takipleştiğin kişilerle sohbet et.</h1>
                <p class="sosial-hero-copy">Mesajlaşma yalnızca iki taraf birbirini takip ettiğinde açılır.</p>
            </div>
            <div class="col-12 col-lg-4">
                <div class="sosial-hero-metrics">
                    <div class="sosial-hero-metric">
                        <span class="sosial-hero-metric-value">{{ $contacts->count() }}</span>
                        <span class="sosial-hero-metric-label">Sohbet edilebilir kişi</span>
                    </div>
                    <div class="sosial-hero-metric">
                        <a class="btn sosial-btn-secondary w-100" href="{{ route('sosial.following') }}">Takip Ettiklerim</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="sosial-chat-shell">
        <aside class="sosial-surface sosial-panel sosial-chat-list">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="sosial-panel-title mb-0">Kişiler</h2>
                <span class="sosial-chip">{{ $contacts->count() }}</span>
            </div>

            <div class="d-flex flex-column gap-2">
                @forelse ($contacts as $contact)
                    <a
                        class="sosial-chat-contact {{ $selectedUser?->id === $contact->id ? 'active' : '' }}"
                        href="{{ route('sosial.messages.show', $contact) }}"
                    >
                        <img src="{{ $contact->avatarUrl() }}" alt="{{ $contact->name }}">
                        <span class="flex-grow-1">
                            <span class="d-block fw-semibold">{{ $contact->name }}</span>
                            <span class="d-block small text-muted">Karşılıklı takip</span>
                        </span>
                        @if (($contact->unread_messages_count ?? 0) > 0)
                            <span class="sosial-chat-unread">{{ $contact->unread_messages_count }}</span>
                        @endif
                    </a>
                @empty
                    <div class="sosial-empty border rounded-4">
                        <div class="sosial-empty-icon"><i class="fa fa-user-plus"></i></div>
                        <div class="fw-semibold mb-1">Henüz sohbet kişisi yok</div>
                        <p class="mb-0">Mesajlaşmak için iki kullanıcının birbirini takip etmesi gerekir.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        <section class="sosial-surface sosial-panel sosial-chat-panel">
            @if ($selectedUser)
                <div class="sosial-chat-header">
                    <img src="{{ $selectedUser->avatarUrl() }}" alt="{{ $selectedUser->name }}">
                    <div class="flex-grow-1">
                        <h2 class="sosial-panel-title mb-1">{{ $selectedUser->name }}</h2>
                        <a class="small text-muted" href="{{ route('sosial.profile.show', $selectedUser) }}">Profili görüntüle</a>
                    </div>
                </div>

                <div class="sosial-chat-messages" id="sosialChatMessages">
                    @forelse ($messages as $message)
                        <div class="sosial-chat-bubble {{ $message->sender_id === auth()->id() ? 'is-own' : 'is-other' }}">
                            <div>{{ $message->body }}</div>
                            <span class="sosial-chat-time">{{ $message->created_at?->format('d.m.Y H:i') }}</span>
                        </div>
                    @empty
                        <div class="sosial-empty border rounded-4">
                            <div class="sosial-empty-icon"><i class="fa fa-comments-o"></i></div>
                            <div class="fw-semibold mb-1">İlk mesajı gönder</div>
                            <p class="mb-0">Bu kişiyle henüz konuşma başlamamış.</p>
                        </div>
                    @endforelse
                </div>

                <form class="sosial-chat-form" method="POST" action="{{ route('sosial.messages.store', $selectedUser) }}">
                    @csrf
                    @include('partials.bot-protection')
                    <label class="form-label fw-semibold" for="messageBody">Mesaj</label>
                    <textarea
                        class="form-control sosial-form-control @error('body') is-invalid @enderror"
                        id="messageBody"
                        name="body"
                        rows="3"
                        maxlength="2000"
                        required
                        placeholder="Mesajını yaz..."
                    >{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn sosial-btn-primary" type="submit">
                            <i class="fa fa-paper-plane"></i> Gönder
                        </button>
                    </div>
                </form>
            @else
                <div class="sosial-empty h-100 d-flex flex-column align-items-center justify-content-center">
                    <div class="sosial-empty-icon"><i class="fa fa-lock"></i></div>
                    <div class="fw-semibold mb-1">Sohbet başlatılamıyor</div>
                    <p class="mb-0">Karşılıklı takip ettiğin kullanıcılar burada listelenecek.</p>
                </div>
            @endif
        </section>
    </div>

    <script>
        (function () {
            const messages = document.getElementById('sosialChatMessages');
            if (messages) messages.scrollTop = messages.scrollHeight;
        })();
    </script>
</x-sossial::layouts.master>
