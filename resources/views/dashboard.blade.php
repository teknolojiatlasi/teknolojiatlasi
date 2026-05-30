<x-app-layout>


    @php
        $user = auth()->user();

        $featuredModules = [
            [
                'title' => 'Yazılar ve İçerikler',
                'description' => 'Yayımlanan yazıları, duyuruları ve yeni içerikleri takip edin.',
                'url' => route('blog.public.index'),
                'cta' => 'Yazılara Git',
                'badge' => 'Güncel',
                'icon' => 'Y',
                'gradient' => 'linear-gradient(135deg, #2563eb 0%, #06b6d4 100%)',
            ],
            [
                'title' => 'CV Oluşturucu',
                'description' => 'CV hazırlayın, güncelleyin ve başvurular için hazır tutun.',
                'url' => route('cv.create'),
                'cta' => 'CV Hazırla',
                'badge' => 'Profil',
                'icon' => 'CV',
                'gradient' => 'linear-gradient(135deg, #10b981 0%, #84cc16 100%)',
            ],
            [
                'title' => 'Soru Platformu',
                'description' => 'Dersleri açın, test çözümlerinizi sürdürün ve ilerlemenizi takip edin.',
                'url' => route('sinav.lessons.index'),
                'cta' => 'Dersleri Aç',
                'badge' => 'Hazırlık',
                'icon' => '?',
                'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #ef4444 100%)',
            ],
            [
                'title' => 'Topluluk',
                'description' => 'Akışa katılın, paylaşımları görün ve toplulukla etkileşim kurun.',
                'url' => route('sosial.feed'),
                'cta' => 'Akışa Git',
                'badge' => 'Topluluk',
                'icon' => 'M',
                'gradient' => 'linear-gradient(135deg, #e11d48 0%, #f97316 100%)',
            ],
            [
                'title' => 'Sosyal Keşfet',
                'description' => 'Yeni başlıkları, etiketleri ve dikkat çeken içerikleri keşfedin.',
                'url' => route('sosial.explore'),
                'cta' => 'Keşfet',
                'badge' => 'Trend',
                'icon' => 'K',
                'gradient' => 'linear-gradient(135deg, #0891b2 0%, #4f46e5 100%)',
            ],
            [
                'title' => 'İletişim',
                'description' => 'Destek, öneri veya iş birliği için hızlıca mesaj gönderin.',
                'url' => route('contact_public_index'),
                'cta' => 'Mesaj Gönder',
                'badge' => 'Destek',
                'icon' => 'İ',
                'gradient' => 'linear-gradient(135deg, #475569 0%, #0f172a 100%)',
            ],
        ];

        if ($activeSurvey) {
            array_unshift($featuredModules, [
                'title' => 'Güncel Anket',
                'description' => $activeSurvey->title,
                'url' => route('survey.public.show', $activeSurvey),
                'cta' => 'Oy Ver',
                'badge' => 'Anket',
                'icon' => 'A',
                'gradient' => 'linear-gradient(135deg, #7c3aed 0%, #ec4899 100%)',
            ]);
        } else {
            array_unshift($featuredModules, [
                'title' => 'Anketler',
                'description' => 'Güncel ve geçmiş anketleri tek sayfadan görüntüleyin.',
                'url' => route('survey.public.index'),
                'cta' => 'Anketlere Git',
                'badge' => 'Anket',
                'icon' => 'A',
                'gradient' => 'linear-gradient(135deg, #7c3aed 0%, #ec4899 100%)',
            ]);
        }

        $quickLinks = [
            ['label' => 'Profilim', 'description' => 'Hesap bilgilerini düzenle', 'url' => route('profile.edit')],
            ['label' => 'Çözümlerim', 'description' => 'Test geçmişini görüntüle', 'url' => route('sinav.attempts.index')],
            ['label' => 'Takip Ettiklerim', 'description' => 'Takip ettiğin hesapları aç', 'url' => route('sosial.following')],
            ['label' => 'Mesajlar', 'description' => 'Sosyal mesaj kutusuna geç', 'url' => route('sosial.messages.index')],
        ];

        $sidebarLinks = [
            ['label' => 'Panel', 'url' => route('dashboard')],
            ['label' => 'Yazılar', 'url' => route('blog.public.index')],
            ['label' => 'CV', 'url' => route('cv.create')],
            ['label' => 'Sosyal', 'url' => route('sosial.feed')],
            ['label' => 'Profil', 'url' => route('profile.edit')],
        ];
    @endphp

    <style>
        .user-dashboard-topbar {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 16px;
            padding: 22px 24px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 28px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
        }

        .user-dashboard-topbar__badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 14px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .user-dashboard-topbar h2 {
            margin: 12px 0 6px;
            font-size: 30px;
            font-weight: 900;
            color: #0f172a;
        }

        .user-dashboard-topbar p {
            margin: 0;
            color: #64748b;
        }

        .user-dashboard-topbar__action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 22px;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 14px 34px rgba(37, 99, 235, 0.24);
        }

        .user-dashboard-page {
            min-height: 100vh;
            padding: 28px 0 36px;
            background: #f8fafc;
        }

        .user-dashboard-shell {
            max-width: 1380px;
            margin: 0 auto;
            padding: 0 16px;
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 24px;
        }

        .user-dashboard-sidebar {
            position: sticky;
            top: 24px;
            align-self: start;
            border-radius: 30px;
            overflow: hidden;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            box-shadow: 0 26px 60px rgba(15, 23, 42, 0.18);
        }

        .user-dashboard-sidebar__hero {
            padding: 24px;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.16), transparent 30%),
                linear-gradient(135deg, #00fdf6 0%, #0f172a 100%);
        }

        .user-dashboard-sidebar__avatar {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            font-size: 22px;
            font-weight: 900;
        }

        .user-dashboard-sidebar__hero h3 {
            margin: 16px 0 6px;
            font-size: 24px;
            font-weight: 900;
        }

        .user-dashboard-sidebar__hero p {
            margin: 0;
            color: rgba(255,255,255,.76);
            line-height: 1.6;
        }

        .user-dashboard-sidebar__section {
            padding: 20px 18px 22px;
        }

        .user-dashboard-sidebar__label {
            margin: 0 0 12px;
            color: rgba(255,255,255,.54);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .user-dashboard-sidebar__nav {
            display: grid;
            gap: 10px;
        }

        .user-dashboard-sidebar__nav a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,.06);
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            transition: transform .2s ease, background .2s ease;
        }

        .user-dashboard-sidebar__nav a:hover {
            transform: translateX(4px);
            background: rgba(255,255,255,.12);
        }

        .user-dashboard-sidebar__stats {
            display: grid;
            gap: 10px;
        }

        .user-dashboard-sidebar__stat {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,.06);
        }

        .user-dashboard-sidebar__stat strong {
            display: block;
            font-size: 26px;
            font-weight: 900;
            color: #fff;
        }

        .user-dashboard-sidebar__stat span {
            color: rgba(255,255,255,.66);
            font-size: 13px;
        }

        .user-dashboard-main {
            display: grid;
            gap: 24px;
        }

        .user-dashboard-panel {
            padding: 22px;
            border: 1px solid #e2e8f0;
            border-radius: 30px;
            background: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        }

        .user-dashboard-panel__head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 16px;
            margin-bottom: 18px;
        }

        .user-dashboard-panel__head h3 {
            margin: 0 0 6px;
            font-size: 26px;
            font-weight: 900;
            color: #0f172a;
        }

        .user-dashboard-panel__head p {
            margin: 0;
            color: #64748b;
        }

        .user-dashboard-quicklinks {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .user-dashboard-quicklinks a {
            display: block;
            padding: 18px;
            border-radius: 22px;
            text-decoration: none;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid #e2e8f0;
            color: #0f172a;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .user-dashboard-quicklinks a:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 32px rgba(15, 23, 42, 0.08);
        }

        .user-dashboard-quicklinks strong {
            display: block;
            font-size: 15px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .user-dashboard-quicklinks span {
            color: #64748b;
            font-size: 13px;
            line-height: 1.55;
        }

        .user-dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .user-dashboard-card {
            position: relative;
            overflow: hidden;
            min-height: 230px;
            padding: 24px;
            border-radius: 30px;
            color: #fff;
            text-decoration: none;
            box-shadow: 0 24px 50px rgba(15, 23, 42, 0.14);
            transition: transform .22s ease, box-shadow .22s ease;
        }

        .user-dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.2);
        }

        .user-dashboard-card::before {
            content: "";
            position: absolute;
            inset: auto -42px -58px auto;
            width: 168px;
            height: 168px;
            border-radius: 50%;
            background: rgba(255,255,255,.18);
        }

        .user-dashboard-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(15,23,42,.18));
        }

        .user-dashboard-card__body {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .user-dashboard-card__top {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 14px;
        }

        .user-dashboard-card__icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.18);
            backdrop-filter: blur(7px);
            font-size: 20px;
            font-weight: 900;
        }

        .user-dashboard-card__badge {
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.18);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .user-dashboard-card h4 {
            margin: 18px 0 10px;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.1;
        }

        .user-dashboard-card p {
            margin: 0;
            color: rgba(255,255,255,.86);
            line-height: 1.65;
        }

        .user-dashboard-card__cta {
            margin-top: auto;
            padding-top: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .user-dashboard-card__cta strong {
            font-size: 15px;
            font-weight: 800;
            color: #fff;
        }

        .user-dashboard-card__cta span {
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.18);
            font-size: 12px;
            font-weight: 800;
            color: #fff;
        }

        @media (max-width: 1199px) {
            .user-dashboard-shell {
                grid-template-columns: 1fr;
            }

            .user-dashboard-sidebar {
                position: static;
            }

            .user-dashboard-quicklinks {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .user-dashboard-topbar,
            .user-dashboard-panel__head {
                flex-direction: column;
                align-items: stretch;
            }

            .user-dashboard-grid,
            .user-dashboard-quicklinks {
                grid-template-columns: 1fr;
            }

            .user-dashboard-card {
                min-height: 210px;
            }
        }
    </style>

    <div class="user-dashboard-page">
        <div class="user-dashboard-shell">
            <aside class="user-dashboard-sidebar">
                <div class="user-dashboard-sidebar__hero">
                    <div class="user-dashboard-sidebar__avatar">
                        {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                    </div>
                    <h3>{{ $user?->name ?? 'Kullanıcı' }}</h3>
                    <p>Solda sabit menü, sağda renkli kartlar. Yönetim paneli hissi kullanıcı tarafına taşındı.</p>
                </div>

                <div class="user-dashboard-sidebar__section">
                    <p class="user-dashboard-sidebar__label">Menü</p>
                    <div class="user-dashboard-sidebar__nav">
                        @foreach ($sidebarLinks as $link)
                            <a href="{{ $link['url'] }}">
                                <span>{{ $link['label'] }}</span>
                                <span>+</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="user-dashboard-sidebar__section">
                    <p class="user-dashboard-sidebar__label">Özet</p>
                    <div class="user-dashboard-sidebar__stats">
                        <div class="user-dashboard-sidebar__stat">
                            <strong>{{ count($featuredModules) }}</strong>
                            <span>Ana modul</span>
                        </div>
                        <div class="user-dashboard-sidebar__stat">
                            <strong>{{ count($quickLinks) }}</strong>
                            <span>Hızlı bağlantı</span>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="user-dashboard-main">
                <section class="user-dashboard-panel">
                    <div class="user-dashboard-panel__head">
                        <div>
                            <h3>Üst Linkler</h3>
                            <p>Ekranın üstündeki menüleri ve bağlantıları kutu içine aldım.</p>
                        </div>
                    </div>

                    <div class="user-dashboard-quicklinks">
                        @foreach ($quickLinks as $link)
                            <a href="{{ $link['url'] }}">
                                <strong>{{ $link['label'] }}</strong>
                                <span>{{ $link['description'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="user-dashboard-panel">
                    <div class="user-dashboard-panel__head">
                        <div>
                            <h3>Renkli Modüller</h3>
                            <p>Yönetime benzer şekilde, tüm modüller büyük ve tıklanabilir kartlar içinde.</p>
                        </div>
                    </div>

                    <div class="user-dashboard-grid">
                        @foreach ($featuredModules as $module)
                            <a href="{{ $module['url'] }}" class="user-dashboard-card" style="background: {{ $module['gradient'] }};">
                                <div class="user-dashboard-card__body">
                                    <div class="user-dashboard-card__top">
                                        <span class="user-dashboard-card__icon">{{ $module['icon'] }}</span>
                                        <span class="user-dashboard-card__badge">{{ $module['badge'] }}</span>
                                    </div>

                                    <div>
                                        <h4>{{ $module['title'] }}</h4>
                                        <p>{{ $module['description'] }}</p>
                                    </div>

                                    <div class="user-dashboard-card__cta">
                                        <strong>{{ $module['cta'] }} -></strong>
                                        <span>Aç</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            </main>
        </div>
    </div>
</x-app-layout>
