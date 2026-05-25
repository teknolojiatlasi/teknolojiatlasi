@extends('app::layouts.admin')

@section('title', 'Yönetim Paneli')

@php
    $dashboardCards = [
        [
            'title' => 'İlan',
            'icon' => 'fas fa-home',
            'description' => 'İlanları görüntüle, yeni içerik ekle ve yayın akışını yönet.',
            'href' => route('blog.index'),
            'gradient' => 'linear-gradient(135deg, #ff7a18 0%, #ffb347 100%)',
            'links' => [
                ['label' => 'İlan listesi', 'href' => route('blog.index')],
                ['label' => 'Yeni ilan', 'href' => route('blog.create')],
            ],
        ],
        [
            'title' => 'Sınav',
            'icon' => 'fas fa-graduation-cap',
            'description' => 'Sınav yapısını, dersleri ve soru akışını tek yerden düzenle.',
            'href' => route('sinav.admin.home'),
            'gradient' => 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)',
            'links' => [
                ['label' => 'Yönetim', 'href' => route('sinav.admin.home')],
                ['label' => 'Dersler', 'href' => route('sinav.admin.lessons.index')],
            ],
        ],
        [
            'title' => 'İletişim',
            'icon' => 'fas fa-envelope-open-text',
            'description' => 'Gelen mesajları incele ve iletişim ayarlarını güncel tut.',
            'href' => route('contact_admin_messages_index'),
            'gradient' => 'linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%)',
            'links' => [
                ['label' => 'Mesajlar', 'href' => route('contact_admin_messages_index')],
                ['label' => 'Ayarlar', 'href' => route('contact_admin_settings_edit')],
            ],
        ],
        [
            'title' => 'CV',
            'icon' => 'fas fa-id-card',
            'description' => 'Yeni CV oluştur, mevcut şablonları düzenle ve çıktıları yönet.',
            'href' => route('cv.create'),
            'gradient' => 'linear-gradient(135deg, #10b981 0%, #34d399 100%)',
            'links' => [
                ['label' => 'CV oluştur', 'href' => route('cv.create')],
            ],
        ],
        [
            'title' => 'Medya',
            'icon' => 'fas fa-photo-video',
            'description' => 'Dosya havuzunu aç, görselleri düzenle ve medya içeriklerini takip et.',
            'href' => route('media.index'),
            'gradient' => 'linear-gradient(135deg, #ec4899 0%, #f97316 100%)',
            'links' => [
                ['label' => 'Medya listesi', 'href' => route('media.index')],
            ],
        ],
        [
            'title' => 'Anket',
            'icon' => 'fas fa-square-poll-vertical',
            'description' => 'Anketleri gözden geçir, yeni form aç ve sonuç akışını kontrol et.',
            'href' => route('survey.index'),
            'gradient' => 'linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%)',
            'links' => [
                ['label' => 'Anketler', 'href' => route('survey.index')],
            ],
        ],
        [
            'title' => 'Oyun',
            'icon' => 'fas fa-gamepad',
            'description' => 'Oyun sayfalarına geç, içerikleri kontrol et ve eşleştirme alanını aç.',
            'href' => route('game.index'),
            'gradient' => 'linear-gradient(135deg, #14b8a6 0%, #22c55e 100%)',
            'links' => [
                ['label' => 'Ana sayfa', 'href' => route('game.index')],
                ['label' => 'Kelime eşleştirme', 'href' => route('game.word-pairs.index')],
            ],
        ],
        [
            'title' => 'Sosial',
            'icon' => 'fas fa-hashtag',
            'description' => 'Sosial postları, tagları ve yorumları admin panelinden yönet.',
            'href' => route('admin.sossial.posts.index'),
            'gradient' => 'linear-gradient(135deg, #f43f5e 0%, #fb7185 50%, #f59e0b 100%)',
            'links' => [
                ['label' => 'Postlar', 'href' => route('admin.sossial.posts.index')],
                ['label' => 'Etiketler', 'href' => route('admin.sossial.tags.index')],
            ],
        ],
        [
            'title' => 'SEO',
            'icon' => 'fas fa-magnifying-glass-chart',
            'description' => 'SEO icin eklenen endpointleri, sitemap akişini ve kritik public sayfalari hizlica kontrol et.',
            'href' => route('sitemap'),
            'gradient' => 'linear-gradient(135deg, #0f766e 0%, #14b8a6 50%, #22c55e 100%)',
            'links' => [
                ['label' => 'Sitemap.xml', 'href' => route('sitemap')],
                ['label' => 'Ana sayfa', 'href' => route('anasayfa')],
                ['label' => 'Ilan arsivi', 'href' => route('blog.public.index')],
                ['label' => 'Anketler', 'href' => route('survey.public.index')],
                ['label' => 'İletişim', 'href' => route('contact_public_index')],
                ['label' => 'Sosial akis', 'href' => route('sosial.feed')],
            ],
        ],
    ];
@endphp

@push('styles')
    <style>
        .admin-dashboard-shell {
            padding: 12px 0 8px;
        }

        .admin-dashboard-hero {
            position: relative;
            overflow: hidden;
            border: 0;
            border-radius: 28px;
            padding: 32px;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.34), transparent 28%),
                linear-gradient(135deg, #0f172a 0%, #00fdf6 48%, #0ea5e9 100%);
            color: #fff;
            box-shadow: 0 28px 60px rgba(15, 23, 42, 0.24);
        }

        .admin-dashboard-hero::after {
            content: "";
            position: absolute;
            inset: auto -60px -80px auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            filter: blur(8px);
        }

        .admin-dashboard-hero h2 {
            margin: 0 0 10px;
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #fff;
        }

        .admin-dashboard-hero p {
            margin: 0;
            max-width: 720px;
            color: rgba(255, 255, 255, 0.86);
            font-size: 15px;
            line-height: 1.7;
        }

        .admin-dashboard-grid {
            margin-top: 26px;
        }

        .admin-dashboard-card {
            position: relative;
            height: 100%;
            min-height: 230px;
            border: 0;
            border-radius: 24px;
            padding: 24px;
            color: #fff;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.24s ease, box-shadow 0.24s ease, filter 0.24s ease;
            box-shadow: 0 22px 48px rgba(15, 23, 42, 0.15);
        }

        .admin-dashboard-card::before {
            content: "";
            position: absolute;
            inset: auto -30px -50px auto;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
        }

        .admin-dashboard-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(15, 23, 42, 0.18));
            pointer-events: none;
        }

        .admin-dashboard-card:hover,
        .admin-dashboard-card:focus-visible {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.22);
            filter: saturate(1.08);
            outline: none;
        }

        .admin-dashboard-card__content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .admin-dashboard-card__head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .admin-dashboard-card__icon {
            width: 58px;
            height: 58px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(6px);
            font-size: 24px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
        }

        .admin-dashboard-card__arrow {
            font-size: 18px;
            opacity: 0.9;
        }

        .admin-dashboard-card h3 {
            margin: 18px 0 10px;
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .admin-dashboard-card p {
            margin: 0;
            color: rgba(255, 255, 255, 0.88);
            line-height: 1.65;
            font-size: 14px;
        }

        .admin-dashboard-card__links {
            position: relative;
            z-index: 2;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: auto;
            padding-top: 22px;
        }

        .admin-dashboard-card__links a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            text-decoration: none;
            color: #fff;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(6px);
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .admin-dashboard-card__links a:hover,
        .admin-dashboard-card__links a:focus-visible {
            color: #fff;
            background: rgba(255, 255, 255, 0.24);
            transform: translateY(-1px);
            outline: none;
        }

        @media (max-width: 767.98px) {
            .admin-dashboard-hero {
                padding: 24px;
                border-radius: 22px;
            }

            .admin-dashboard-hero h2 {
                font-size: 24px;
            }

            .admin-dashboard-card {
                min-height: 210px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="admin-dashboard-shell">
        <div class="x_panel" style="border: 0; box-shadow: none; background: transparent;">
            <div class="x_content" style="padding: 0;">
                <section class="admin-dashboard-hero">
                    <h2>Yönetim Paneli</h2>
                    <p>
                        Tüm yönetim alanları tek ekranda. Kartların tamamı tıklanabilir; hızlı geçiş için alt bağlantıları da
                        kullanabilirsiniz.
                    </p>
                </section>

                <div class="row admin-dashboard-grid">
                    @foreach ($dashboardCards as $card)
                        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                            <article
                                class="admin-dashboard-card"
                                style="background: {{ $card['gradient'] }};"
                                data-href="{{ $card['href'] }}"
                                tabindex="0"
                                role="link"
                                aria-label="{{ $card['title'] }} bölümüne git"
                            >
                                <div class="admin-dashboard-card__content">
                                    <div class="admin-dashboard-card__head">
                                        <span class="admin-dashboard-card__icon">
                                            <i class="{{ $card['icon'] }}"></i>
                                        </span>
                                        <span class="admin-dashboard-card__arrow">
                                            <i class="fas fa-arrow-up-right-from-square"></i>
                                        </span>
                                    </div>

                                    <div>
                                        <h3>{{ $card['title'] }}</h3>
                                        <p>{{ $card['description'] }}</p>
                                    </div>

                                    <div class="admin-dashboard-card__links">
                                        @foreach ($card['links'] as $link)
                                            <a href="{{ $link['href'] }}">
                                                <span>{{ $link['label'] }}</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.admin-dashboard-card[data-href]').forEach(function(card) {
            card.addEventListener('click', function(event) {
                if (event.target.closest('a')) {
                    return;
                }

                window.location.href = card.dataset.href;
            });

            card.addEventListener('keydown', function(event) {
                if (event.key !== 'Enter' && event.key !== ' ') {
                    return;
                }

                event.preventDefault();
                window.location.href = card.dataset.href;
            });
        });
    </script>
@endpush
