<x-app-layout>
    @php
        $simulationCount = $simulations->count();
        $descendantCount = max($category->subtreeIds()->count() - 1, 0);
    @endphp

    <style>
        .simulation-category-page {
            min-height: 100vh;
            padding: 28px 0 36px;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.08), transparent 28%),
                linear-gradient(180deg, #f8fafc 0%, #eef4ff 100%);
        }

        .simulation-category-shell {
            max-width: 1380px;
            margin: 0 auto;
            padding: 0 16px;
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            gap: 24px;
        }

        .simulation-category-sidebar {
            position: sticky;
            top: 24px;
            align-self: start;
            border-radius: 30px;
            overflow: hidden;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            box-shadow: 0 26px 60px rgba(15, 23, 42, 0.18);
        }

        .simulation-category-sidebar__hero {
            padding: 24px;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.16), transparent 30%),
                linear-gradient(135deg, #1d4ed8 0%, #0f172a 100%);
        }

        .simulation-category-sidebar__icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            font-size: 24px;
            font-weight: 900;
        }

        .simulation-category-sidebar__hero h1 {
            margin: 16px 0 6px;
            font-size: 24px;
            font-weight: 900;
        }

        .simulation-category-sidebar__hero p {
            margin: 0;
            color: rgba(255,255,255,.76);
            line-height: 1.6;
        }

        .simulation-category-sidebar__section {
            padding: 20px 18px 22px;
        }

        .simulation-category-sidebar__label {
            margin: 0 0 12px;
            color: rgba(255,255,255,.54);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .simulation-category-sidebar__stats {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .simulation-category-sidebar__stat {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,.06);
        }

        .simulation-category-sidebar__stat strong {
            display: block;
            font-size: 26px;
            font-weight: 900;
            color: #fff;
        }

        .simulation-category-sidebar__stat span {
            color: rgba(255,255,255,.66);
            font-size: 13px;
        }

        .simulation-category-tree,
        .simulation-category-tree ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .simulation-category-tree {
            display: grid;
            gap: 10px;
        }

        .simulation-tree-item {
            margin: 0;
        }

        .simulation-tree-item + .simulation-tree-item {
            margin-top: 8px;
        }

        .simulation-tree-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 13px 16px;
            border-radius: 18px;
            color: #fff;
            text-decoration: none;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.08);
            margin-left: calc(var(--level, 0) * 12px);
            transition: transform .2s ease, background .2s ease, border-color .2s ease;
        }

        .simulation-tree-link:hover {
            transform: translateX(4px);
            background: rgba(255,255,255,.12);
            border-color: rgba(255,255,255,.18);
        }

        .simulation-tree-link.is-active {
            background: linear-gradient(135deg, rgba(59, 130, 246, .85), rgba(14, 165, 233, .75));
            border-color: rgba(191, 219, 254, .95);
            box-shadow: 0 12px 28px rgba(14, 165, 233, .2);
        }

        .simulation-tree-link__name {
            font-weight: 700;
            line-height: 1.45;
        }

        .simulation-tree-link__meta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            font-size: 12px;
            font-weight: 800;
        }

        .simulation-category-main {
            display: grid;
            gap: 24px;
        }

        .simulation-category-topbar,
        .simulation-category-panel {
            border: 1px solid #dbe5f1;
            border-radius: 30px;
            background: rgba(255,255,255,.96);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        }

        .simulation-category-topbar {
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 18px;
        }

        .simulation-category-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 14px;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .simulation-category-topbar h2 {
            margin: 12px 0 8px;
            font-size: 32px;
            font-weight: 900;
            color: #0f172a;
        }

        .simulation-category-topbar p {
            margin: 0;
            color: #64748b;
            line-height: 1.65;
            max-width: 720px;
        }

        .simulation-category-topbar__action {
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

        .simulation-category-panel {
            padding: 22px;
        }

        .simulation-category-panel__head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 16px;
            margin-bottom: 18px;
        }

        .simulation-category-panel__head h3 {
            margin: 0 0 6px;
            font-size: 26px;
            font-weight: 900;
            color: #0f172a;
        }

        .simulation-category-panel__head p {
            margin: 0;
            color: #64748b;
        }

        .simulation-category-trail {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .simulation-category-trail span {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 700;
        }

        .simulation-category-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .simulation-card {
            position: relative;
            overflow: hidden;
            min-height: 240px;
            padding: 24px;
            border-radius: 28px;
            text-decoration: none;
            color: #fff;
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 50%, #06b6d4 100%);
            box-shadow: 0 24px 50px rgba(15, 23, 42, 0.14);
            transition: transform .22s ease, box-shadow .22s ease;
        }

        .simulation-card:nth-child(4n + 2) {
            background: linear-gradient(135deg, #14532d 0%, #16a34a 55%, #84cc16 100%);
        }

        .simulation-card:nth-child(4n + 3) {
            background: linear-gradient(135deg, #7c2d12 0%, #f97316 55%, #facc15 100%);
        }

        .simulation-card:nth-child(4n + 4) {
            background: linear-gradient(135deg, #581c87 0%, #7c3aed 55%, #ec4899 100%);
        }

        .simulation-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.2);
        }

        .simulation-card::before {
            content: "";
            position: absolute;
            inset: auto -42px -58px auto;
            width: 168px;
            height: 168px;
            border-radius: 50%;
            background: rgba(255,255,255,.18);
        }

        .simulation-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(15,23,42,.18));
        }

        .simulation-card__body {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .simulation-card__top {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 14px;
        }

        .simulation-card__icon {
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

        .simulation-card__badge {
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.18);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .simulation-card h4 {
            margin: 18px 0 10px;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.1;
        }

        .simulation-card p {
            margin: 0;
            color: rgba(255,255,255,.88);
            line-height: 1.65;
        }

        .simulation-card__cta {
            margin-top: auto;
            padding-top: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .simulation-card__cta strong {
            font-size: 15px;
            font-weight: 800;
            color: #fff;
        }

        .simulation-card__cta span {
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.18);
            font-size: 12px;
            font-weight: 800;
            color: #fff;
        }

        .simulation-category-empty {
            padding: 24px;
            border-radius: 24px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #475569;
        }

        @media (max-width: 1199px) {
            .simulation-category-shell {
                grid-template-columns: 1fr;
            }

            .simulation-category-sidebar {
                position: static;
            }
        }

        @media (max-width: 767px) {
            .simulation-category-topbar,
            .simulation-category-panel__head {
                flex-direction: column;
                align-items: stretch;
            }

            .simulation-category-grid {
                grid-template-columns: 1fr;
            }

            .simulation-card {
                min-height: 220px;
            }
        }
    </style>

    <div class="simulation-category-page">
        <div class="simulation-category-shell">
            <aside class="simulation-category-sidebar">
                <div class="simulation-category-sidebar__hero">
                    <div class="simulation-category-sidebar__icon">
                        {{ strtoupper(substr($category->name, 0, 1)) }}
                    </div>
                    <h1>{{ $category->name }}</h1>
                    <p>{{ $category->description ?: 'Bu kategori altindaki tum simulasyonlari soldaki kategori menusu ile birlikte daha duzenli bir panelde topladim.' }}</p>
                </div>

                <div class="simulation-category-sidebar__section">
                    <p class="simulation-category-sidebar__label">Kategoriler</p>
                    <nav>
                        <ul class="simulation-category-tree">
                            @include('simulation::partials.category_tree_item', [
                                'node' => $category,
                                'level' => 0,
                                'currentCategoryId' => $category->id,
                            ])
                        </ul>
                    </nav>
                </div>

                <div class="simulation-category-sidebar__section">
                    <p class="simulation-category-sidebar__label">Ozet</p>
                    <div class="simulation-category-sidebar__stats">
                        <div class="simulation-category-sidebar__stat">
                            <strong>{{ $simulationCount }}</strong>
                            <span>Yayinlanmis simulasyon</span>
                        </div>
                        <div class="simulation-category-sidebar__stat">
                            <strong>{{ $descendantCount }}</strong>
                            <span>Alt kategori</span>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="simulation-category-main">
                <section class="simulation-category-topbar">
                    <div>
                        <span class="simulation-category-badge">Simulasyon Kategorisi</span>
                        <h2>{{ $category->name }}</h2>
                        <p>{{ $category->description ?: 'Kategori sayfasi dashboard yapisina cekildi; solda sabit kategori paneli, sagda ise okunakli simulasyon kartlari yer aliyor.' }}</p>
                    </div>

                    <a href="{{ route('simulation.index') }}" class="simulation-category-topbar__action">Tum simulasyonlar</a>
                </section>

                <section class="simulation-category-panel">
                    <div class="simulation-category-panel__head">
                        <div>
                            <h3>Konum</h3>
                            <p>Su an bulundugunuz kategori yolu.</p>
                        </div>
                    </div>

                    <div class="simulation-category-trail">
                        @foreach ($categoryTrail as $trailItem)
                            <span>{{ $trailItem->name }}</span>
                        @endforeach
                    </div>
                </section>

                <section class="simulation-category-panel">
                    <div class="simulation-category-panel__head">
                        <div>
                            <h3>Simulasyonlar</h3>
                            <p>Bu kategori agaci altinda yayinlanan tum icerikler.</p>
                        </div>
                    </div>

                    @if ($simulations->isEmpty())
                        <div class="simulation-category-empty">
                            Bu kategori agaci altinda yayinlanmis simulasyon bulunmuyor.
                        </div>
                    @else
                        <div class="simulation-category-grid">
                            @foreach ($simulations as $simulation)
                                <a href="{{ route('simulation.show', $simulation->slug) }}" class="simulation-card">
                                    <div class="simulation-card__body">
                                        <div class="simulation-card__top">
                                            <span class="simulation-card__icon">{{ strtoupper(substr($simulation->title, 0, 1)) }}</span>
                                            <span class="simulation-card__badge">{{ $simulation->category?->name ?: 'Simulasyon' }}</span>
                                        </div>

                                        <div>
                                            <h4>{{ $simulation->title }}</h4>
                                            <p>{{ $simulation->excerpt ?: 'Bu simulasyonu acarak icerige ve etkilesimli deneyime ulasabilirsiniz.' }}</p>
                                        </div>

                                        <div class="simulation-card__cta">
                                            <strong>Simulasyonu Ac</strong>
                                            <span>Detay</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>
            </main>
        </div>
    </div>
</x-app-layout>
