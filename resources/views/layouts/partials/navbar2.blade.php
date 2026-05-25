@php
    $resolveIconClass = static function (?string $icon): string {
        $icon = trim((string) $icon);

        if ($icon === '') {
            return 'fa fa-circle';
        }

        if (str_contains($icon, ' ')) {
            return $icon;
        }

        if (str_starts_with($icon, 'fa-')) {
            return 'fa ' . $icon;
        }

        if (in_array($icon, ['fa', 'fas', 'far', 'fab', 'fal', 'fat'], true)) {
            return $icon;
        }

        return 'fa ' . $icon;
    };

    $socialNavLinks = [
        ['label' => 'Tüm Memur İlanları', 'icon' => 'fa fa-globe', 'url' => 'https://teknolojiatlasi.com.tr'],
        ['label' => 'Instagram', 'icon' => 'fab fa-instagram', 'url' => 'https://instagram.com/teknolojiatlasi'],
        ['label' => 'Sınava Hazırlık', 'icon' => 'fab fa-youtube', 'url' => 'https://youtube.com/channel/UC2GYfggaXT0uNuxfvU690KA/videos'],
        ['label' => 'Telegram', 'icon' => 'fab fa-telegram', 'url' => 'https://t.me/teknolojiatlasi'],
        ['label' => 'Twitter', 'icon' => 'fab fa-twitter', 'url' => 'https://twitter.com/teknolojiatlasi'],
    ];

    $contextTitle = null;
    $contextIcon = 'fa-bars';
    $contextLinks = [];

    if (request()->routeIs('anasayfa')) {
        $contextTitle = 'Sosyal Medya';
        $contextIcon = 'fa-share-alt';
        $contextLinks = array_map(fn ($item) => $item + ['active' => false], $socialNavLinks);
    } elseif (request()->routeIs('sinav.*')) {
        $contextTitle = 'Soru Platformu';
        $contextIcon = 'fa-question-circle';
        $contextLinks = [
            ['label' => 'Tüm Dersler', 'icon' => 'fa-book', 'url' => route('sinav.lessons.index'), 'active' => request()->routeIs('sinav.lessons.*')],
            ['label' => 'Çözümlerim', 'icon' => 'fa-check-square-o', 'url' => auth()->check() ? route('sinav.attempts.index') : route('login', ['redirect' => url()->current()]), 'active' => request()->routeIs('sinav.attempts.*')],
        ];
    } elseif (request()->routeIs('simulation.*')) {
        $contextTitle = 'Simulasyonlar';
        $contextIcon = 'fa-flask';
        $contextLinks = [
            ['label' => 'Tum Simulasyonlar', 'icon' => 'fa-list', 'url' => route('simulation.index'), 'active' => request()->routeIs('simulation.index')],
        ];
    } elseif (request()->routeIs('blog.public.*')) {
        $contextTitle = 'İlan';
        $contextIcon = 'fa-pencil';
        $contextLinks = [
            ['label' => 'Ana Sayfa', 'icon' => 'fa-home', 'url' => route('anasayfa'), 'active' => false],
            ['label' => 'Tüm Yazılar', 'icon' => 'fa-newspaper-o', 'url' => route('blog.public.index'), 'active' => request()->routeIs('blog.public.index')],
        ];

        if (request()->routeIs('blog.public.index') && isset($menus) && $menus->isNotEmpty()) {
            foreach ($menus as $category) {
                $contextLinks[] = [
                    'label' => $category->name . ' (' . $category->blogs_count . ')',
                    'type' => 'category',
                    'child' => false,
                    'url' => route('blog.public.index', ['category' => $category->slug ?: $category->id]),
                    'active' => isset($selectedCategory) && $selectedCategory?->id === $category->id,
                ];

                foreach ($category->children as $child) {
                    $contextLinks[] = [
                        'label' => $child->name . ' (' . $child->blogs_count . ')',
                        'type' => 'category',
                        'child' => true,
                        'url' => route('blog.public.index', ['category' => $child->slug ?: $child->id]),
                        'active' => isset($selectedCategory) && $selectedCategory?->id === $child->id,
                    ];
                }
            }
        }
    } elseif (request()->routeIs('cv.*')) {
        $contextTitle = 'CV Modülü';
        $contextIcon = 'fa-id-card';
        $contextLinks = [
            ['label' => 'CV Oluştur', 'icon' => 'fa-circle-plus', 'url' => route('cv.create'), 'active' => request()->routeIs('cv.create')],
        ];
    } elseif (request()->routeIs('survey.*')) {
        $contextTitle = 'Anketler';
        $contextIcon = 'fa-bar-chart';
        $contextLinks = [
            ['label' => 'Güncel Anket', 'icon' => 'fa-bullseye', 'url' => route('anasayfa') . '#active-survey', 'active' => false],
            ['label' => 'Tüm Anketler', 'icon' => 'fa-list', 'url' => route('survey.public.index'), 'active' => request()->routeIs('survey.public.index')],
        ];
    } elseif (request()->routeIs('sosial.*')) {
        $contextTitle = 'Topluluk';
        $contextIcon = 'fa-users';
        $contextLinks = [
            ['label' => 'Akış', 'icon' => 'fa-rss', 'url' => route('sosial.feed'), 'active' => request()->routeIs('sosial.feed')],
            ['label' => 'Keşfet', 'icon' => 'fa-hashtag', 'url' => route('sosial.explore'), 'active' => request()->routeIs('sosial.explore') || request()->routeIs('sosial.tags.*')],
        ];

        if (auth()->check()) {
            $contextLinks[] = ['label' => 'Takip', 'icon' => 'fa-user-plus', 'url' => route('sosial.following'), 'active' => request()->routeIs('sosial.following')];
            $contextLinks[] = ['label' => 'Mesajlar', 'icon' => 'fa-comments', 'url' => route('sosial.messages.index'), 'active' => request()->routeIs('sosial.messages.*')];
        }
    } elseif (request()->routeIs('contact_public_*')) {
        $contextTitle = 'İletişim';
        $contextIcon = 'fa-envelope';
        $contextLinks = [
            ['label' => 'İletişim Formu', 'icon' => 'fa-paper-plane', 'url' => route('contact_public_index'), 'active' => request()->routeIs('contact_public_*')],
        ];
    }
@endphp

<nav class="navbar navbar-expand-lg navbar-dark sticky-top navbar-blur shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('anasayfa') }}">
            <img src="{{ asset('favicon-star.svg') }}" alt="" width="30" height="30" class="d-inline-block align-text-top">
            <span>Teknoloji Atlası</span>
        </a>

        <button class="navbar-toggler" type="button" aria-controls="navMenu" aria-expanded="false" aria-label="Menüyü aç veya kapat">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('anasayfa') ? 'active' : '' }}" href="{{ route('anasayfa') }}">
                        <i class="fa fa-home me-1"></i> Ana Sayfa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('blog.public.*') ? 'active' : '' }}" href="{{ route('blog.public.index') }}">
                        <i class="fa fa-pencil me-1"></i> İlan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cv.*') ? 'active' : '' }}" href="{{ route('cv.create') }}">
                        <i class="fa fa-id-card me-1"></i> CV Oluştur
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sinav.*') ? 'active' : '' }}" href="{{ route('sinav.lessons.index') }}">
                        <i class="fa fa-question-circle me-1"></i> Soru Platformu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('simulation.*') ? 'active' : '' }}" href="{{ route('simulation.index') }}">
                        <i class="fa fa-flask me-1"></i> Simulasyonlar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('survey.public.*') ? 'active' : '' }}" href="{{ route('survey.public.index') }}">
                        <i class="fa fa-bar-chart me-1"></i> Anketler
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('sosial.*') ? 'active' : '' }}" href="{{ route('sosial.feed') }}">
                        <i class="fa fa-users me-1"></i> Topluluk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact_public_*') ? 'active' : '' }}" href="{{ route('contact_public_index') }}">
                        <i class="fa fa-envelope me-1"></i> İletişim
                    </a>
                </li>
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login', ['redirect' => url()->current()]) }}">
                            <i class="fa fa-sign-in me-1"></i> Giriş
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-light rounded-pill px-3">
                                <i class="fa fa-sign-out me-1"></i> Çıkış Yap
                            </button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

@if(!empty($contextLinks))
    <div class="social-strip">
        <div class="container">
            <div class="social-strip-inner">
                <div class="social-strip-title">
                    <i class="{{ $resolveIconClass($contextIcon) }} text-primary"></i>
                    <span>{{ $contextTitle }}</span>
                </div>

                <div class="social-strip-links">
                    @foreach($contextLinks as $contextLink)
                        @if(($contextLink['type'] ?? 'link') === 'label')
                            <span class="social-strip-label {{ !empty($contextLink['child']) ? 'is-child' : '' }}">
                                {{ $contextLink['label'] }}
                            </span>
                        @elseif(($contextLink['type'] ?? 'link') === 'category')
                            <a
                                href="{{ $contextLink['url'] }}"
                                class="social-strip-link {{ !empty($contextLink['active']) ? 'active' : '' }} {{ !empty($contextLink['child']) ? '' : '' }}"
                            >
                                <span>{{ $contextLink['label'] }}</span>
                            </a>
                        @else
                            <a
                                href="{{ $contextLink['url'] }}"
                                class="social-strip-link {{ !empty($contextLink['active']) ? 'active' : '' }}"
                                @if($contextLink['url'] !== '#') target="{{ str_starts_with($contextLink['url'], 'http') ? '_blank' : '_self' }}" @endif
                                @if(str_starts_with($contextLink['url'], 'http')) rel="noopener" @endif
                            >
                                <i class="{{ $resolveIconClass($contextLink['icon'] ?? '') }}"></i>
                                <span>{{ $contextLink['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
