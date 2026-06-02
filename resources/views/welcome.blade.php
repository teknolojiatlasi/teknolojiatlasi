@extends('layouts.app2')

@section('title', 'Ana Sayfa | Teknoloji Atlası')

@push('styles')
<style>
    .home-shell { --dark:#0f172a; --accent:#f59e0b; --line:rgba(15,23,42,.08); --surface:rgba(255,255,255,.88); }
    .home-shell > .container { max-width:1240px; padding-left:clamp(.9rem,2.4vw,1.6rem); padding-right:clamp(.9rem,2.4vw,1.6rem); }
    .home-shell .section-block { padding: 1.75rem 0; }
    .home-shell .glass-card { background: var(--surface); border: 1px solid var(--line); border-radius: 1.5rem; box-shadow: 0 20px 50px rgba(15,23,42,.08); backdrop-filter: blur(10px); }
    .home-shell .section-kicker { display:inline-flex; align-items:center; gap:.5rem; padding:.45rem .9rem; border-radius:999px; background:rgba(245,158,11,.12); color:#92400e; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; }
    .home-shell .section-title { font-size:clamp(1.8rem,2.4vw,3rem); font-weight:900; line-height:1.05; letter-spacing:-.03em; margin:.9rem 0; }
    .home-shell .section-copy { color:#475569; max-width:62ch; line-height:1.8; }
    .hero-block { position:relative; overflow:hidden; padding:1.5rem; border-radius:2rem; background:radial-gradient(circle at top left, rgba(245,158,11,.25), transparent 26%), linear-gradient(135deg,#0f172a 0%,#00fdf6 60%,#2563eb 100%); color:#fff; box-shadow:0 32px 80px rgba(15,23,42,.22); }
    .hero-news-layout { display:grid; grid-template-columns:minmax(0,2.35fr) minmax(280px,1fr); gap:1.6rem; align-items:stretch; }
    .hero-main-panel { background:#f8fafc; border-radius:1rem; overflow:hidden; box-shadow:0 18px 48px rgba(15,23,42,.08); }
    .hero-main-panel,
    .hero-side-grid { min-height:344px; }
    .hero-carousel-nav { display:flex; flex-wrap:wrap; justify-content:center; gap:.35rem; margin-bottom:1rem; }
    .hero-carousel-nav.hero-carousel-nav-bottom { justify-content:flex-start; margin:0; padding:1.15rem 1.5rem 1.2rem; background:#fff; border-top:1px solid rgba(15,23,42,.08); }
    .hero-carousel-nav [data-bs-target], .hero-carousel-nav .hero-all-link { width:2rem; height:2rem; border-radius:999px; border:0; background:#e5e7eb; color:#111827; font-weight:800; line-height:1; display:inline-flex; align-items:center; justify-content:center; text-decoration:none; opacity:1; }
    .hero-carousel-nav [data-bs-target].active { background:#0b7ed3; color:#fff; }
    .hero-carousel-nav .hero-all-link { width:auto; padding:0 .75rem; border-radius:.2rem; }
    .hero-carousel { position:relative; overflow:hidden; background:#0f172a; border-radius:1rem 1rem 0 0; }
    .hero-carousel .carousel-inner, .hero-carousel .carousel-item { height:344px; }
    .hero-carousel-slide { position:relative; height:344px; overflow:hidden; display:block; color:inherit; text-decoration:none; }
    .hero-carousel-slide:hover { color:inherit; text-decoration:none; }
    .hero-carousel-slide img { width:100%; height:100%; object-fit:cover; object-position:center; display:block; filter:saturate(1.02); }
    .hero-carousel-slide::before { content:""; position:absolute; inset:0; background:linear-gradient(90deg, rgba(15,23,42,.42), rgba(15,23,42,.08)); z-index:1; }
    .hero-carousel-content { position:absolute; z-index:2; left:3.1rem; right:3.1rem; bottom:1.55rem; color:#fff; }
    .hero-carousel-title { display:inline-block; max-width:88%; margin:0 0 .55rem; padding:.7rem .9rem; background:#dc2f45; color:#fff; font-size:clamp(1.45rem,2.35vw,2.15rem); font-weight:900; line-height:1.15; letter-spacing:0; text-transform:uppercase; }
    .hero-carousel-copy { display:block; width:max-content; max-width:78%; margin:0; padding:.45rem .7rem; background:rgba(0,0,0,.82); color:#fff; font-size:.98rem; font-weight:700; line-height:1.35; }
    .hero-carousel-empty { min-height:312px; display:flex; align-items:center; padding:2rem; color:#fff; background:linear-gradient(135deg,#0f172a,#00fdf6); }
    .hero-carousel .carousel-control-prev, .hero-carousel .carousel-control-next { width:4rem; opacity:1; z-index:3; }
    .hero-carousel .carousel-control-prev-icon, .hero-carousel .carousel-control-next-icon { width:2rem; height:2rem; background-size:1.4rem; filter:none; }
    .hero-side-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); grid-template-rows:repeat(2,minmax(0,1fr)); gap:1.8rem; padding-top:0; align-content:stretch; }
    .hero-side-card {
        display:block;
        height:100%;
        background:#fff;
        color:#111827;
        text-decoration:none;
        overflow:hidden;
        border-radius:.2rem;
        border:1px solid rgba(15,23,42,.06);
        box-shadow:none;
    }
    .hero-side-card:hover {
        color:#111827;
        transform:translateY(-4px);
        box-shadow:0 18px 40px rgba(15,23,42,.12);
    }
    .hero-side-card img, .hero-side-placeholder {
        width:100%;
        height:138px;
        object-fit:cover;
        object-position:center;
        display:block;
        background:linear-gradient(135deg,#dbeafe,#e2e8f0);
    }
    .hero-side-title {
        display:-webkit-box;
        min-height:4.65rem;
        padding:.7rem .8rem .55rem;
        font-size:.75rem;
        font-weight:900;
        line-height:1.35;
        overflow:hidden;
        -webkit-line-clamp:3;
        -webkit-box-orient:vertical;
    }
    .hero-title { font-size:clamp(1.9rem,3vw,3.5rem); font-weight:900; line-height:1; letter-spacing:-.05em; margin:.8rem 0; max-width:10ch; }
    .hero-copy { color:rgba(255,255,255,.8); line-height:1.8; max-width:58ch; }
    .hero-actions .btn, .home-shell .rounded-pill.btn { padding:.8rem 1.2rem; font-weight:700; }
    .hero-stat-value { display:block; font-size:1.55rem; font-weight:900; line-height:1; }
    .hero-stat-label { color:rgba(255,255,255,.7); font-size:.88rem; }
    .hero-story { overflow:hidden; border-radius:1.5rem; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.16); }
    .hero-story img, .story-image { width:100%; object-fit:cover; background:linear-gradient(135deg,#dbeafe,#e2e8f0); }
    .hero-story img { height:300px; }
    .story-image { aspect-ratio:16/9; }
    .hero-story-body, .story-body, .mini-story-body, .card-body-pad { padding:1.25rem; }
    .eyebrow { color:#64748b; font-size:.8rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
    .eyebrow-light { color:rgba(255,255,255,.72); }
    .story-link, .module-title, .lesson-title { color:var(--dark); text-decoration:none; font-weight:800; }
    .story-link { font-size:1rem; line-height:1.3; }
    .story-meta, .mini-meta { color:#64748b; font-size:.88rem; }
    .story-card, .module-card, .lesson-card, .category-card, .survey-card, .mini-story { transition:transform .22s ease, box-shadow .22s ease; }
    .story-card {
        background:#fff;
        border:1px solid rgba(15,23,42,.06);
        border-radius:.2rem;
        box-shadow:none;
        backdrop-filter:none;
    }
    .story-card .story-body { padding:1rem .95rem 1.1rem; }
    .story-card .story-image {
        aspect-ratio: auto;
        height: 150px;
        border-radius:0;
    }
    .story-card .story-meta,
    .story-card .story-excerpt,
    .story-card .btn { display:none !important; }
    .story-card .story-link {
        display:-webkit-box;
        margin:0 !important;
        color:#111827;
        font-size:1rem;
        font-weight:900;
        line-height:1.45;
        overflow:hidden;
        -webkit-line-clamp:3;
        -webkit-box-orient:vertical;
    }
    .story-card:hover { transform:translateY(-4px); box-shadow:0 18px 40px rgba(15,23,42,.12); }
    .story-card:hover .story-link { color:#0b7ed3; }
    .games-promo-card {
        display:grid;
        grid-template-columns:minmax(0,1fr) auto;
        gap:1.25rem;
        align-items:center;
        margin-bottom:1.35rem;
        padding:1.15rem;
        border-radius:.75rem;
        background:
            linear-gradient(135deg, rgba(14,165,233,.12), rgba(245,158,11,.12)),
            #fff;
        border:1px solid rgba(15,23,42,.08);
        box-shadow:0 16px 36px rgba(15,23,42,.08);
        text-decoration:none;
        color:#0f172a;
    }
    .games-promo-card:hover {
        color:#0f172a;
        transform:translateY(-3px);
        box-shadow:0 22px 48px rgba(15,23,42,.12);
        text-decoration:none;
    }
    .games-promo-content {
        display:flex;
        gap:1rem;
        align-items:center;
        min-width:0;
    }
    .games-promo-icon {
        display:inline-flex;
        width:3rem;
        height:3rem;
        flex:0 0 3rem;
        align-items:center;
        justify-content:center;
        border-radius:.75rem;
        background:#0ea5e9;
        color:#fff;
        font-size:1.25rem;
    }
    .games-promo-card h3 {
        margin:0 0 .25rem;
        font-size:1.15rem;
        font-weight:900;
        line-height:1.2;
    }
    .games-promo-card p {
        margin:0;
        color:#64748b;
        line-height:1.5;
    }
    .games-promo-action {
        display:inline-flex;
        gap:.45rem;
        align-items:center;
        justify-content:center;
        padding:.75rem 1rem;
        border-radius:999px;
        background:#f59e0b;
        color:#111827;
        font-weight:800;
        white-space:nowrap;
    }
    .module-card:hover, .lesson-card:hover, .mini-story:hover { transform:translateY(-4px); box-shadow:0 24px 60px rgba(15,23,42,.14); }
    .feature-slide { position:relative; min-height:460px; border-radius:1.5rem; overflow:hidden; background-size:cover; background-position:center; }
    .feature-slide::before { content:""; position:absolute; inset:0; background:linear-gradient(180deg, rgba(15,23,42,.18), rgba(15,23,42,.9)); }
    .feature-slide-content { position:absolute; inset:auto 0 0 0; padding:2rem; color:#fff; }
    .feature-slide-title { font-size:clamp(1.7rem,2vw,2.5rem); font-weight:800; line-height:1.08; margin:.8rem 0; max-width:18ch; }
    .feature-slide-copy { max-width:56ch; color:rgba(255,255,255,.82); }
    .category-item + .category-item { margin-top:1rem; padding-top:1rem; border-top:1px solid rgba(148,163,184,.2); }
    .category-pill, .topic-pill, .survey-stat { display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .72rem; border-radius:999px; font-size:.82rem; font-weight:600; }
    .category-pill { margin:0 .45rem .45rem 0; background:#eff6ff; color:#00fdf6; }
    .topic-pill { margin:0 .45rem .45rem 0; background:#f8fafc; border:1px solid rgba(148,163,184,.2); color:#334155; }
    .survey-stat { background:#fff; color:#334155; font-size:.88rem; }
    .mini-story {
        display:flex;
        gap:1rem;
        overflow:hidden;
        align-items:flex-start;
        padding:.9rem;
        border-radius:1.6rem;
        background:rgba(255,255,255,.94);
        border:1px solid rgba(148,163,184,.14);
        box-shadow:0 18px 44px rgba(15,23,42,.08);
    }
    .mini-story img {
        width:96px;
        height:96px;
        flex:0 0 96px;
        object-fit:cover;
        object-position:center;
        background:linear-gradient(135deg,#dbeafe,#e2e8f0);
        padding:0;
        border-radius:1.15rem;
        box-shadow:inset 0 0 0 1px rgba(255,255,255,.4);
    }
    .mini-story-body {
        flex:1 1 auto;
        min-width:0;
        padding:.1rem .1rem .1rem .25rem;
    }
    .mini-story .mini-meta {
        display:block;
        margin-bottom:.55rem;
        color:#64748b;
        font-size:.85rem;
        font-weight:600;
        line-height:1.35;
    }
    .mini-story .story-link {
        display:-webkit-box;
        margin-bottom:.7rem !important;
        font-size:1rem;
        font-weight:900;
        line-height:1.3;
        overflow:hidden;
        -webkit-line-clamp:3;
        -webkit-box-orient:vertical;
    }
    .mini-story .text-secondary.small {
        display:-webkit-box;
        margin-bottom:.95rem !important;
        color:#64748b !important;
        font-size:.95rem !important;
        line-height:1.55;
        overflow:hidden;
        -webkit-line-clamp:3;
        -webkit-box-orient:vertical;
    }
    .mini-story .mini-story-cta {
        display:inline-flex;
        align-items:center;
        gap:.45rem;
        color:#2563eb;
        font-size:.92rem;
        font-weight:800;
        text-decoration:none;
    }
    .mini-story .mini-story-cta:hover {
        color:#00fdf6;
    }
    .module-icon { width:56px; height:56px; border-radius:1rem; display:inline-flex; align-items:center; justify-content:center; font-size:1.35rem; color:#fff; margin-bottom:1rem; }
    .module-blue { background:linear-gradient(135deg,#2563eb,#00fdf6); } .module-green { background:linear-gradient(135deg,#059669,#10b981); } .module-orange { background:linear-gradient(135deg,#d97706,#f59e0b); } .module-slate { background:linear-gradient(135deg,#334155,#0f172a); }
    .simulation-category-card .module-title { display:block; margin-bottom:.7rem; }
    .simulation-category-card .simulation-category-description { display:-webkit-box; min-height:4.8rem; overflow:hidden; -webkit-line-clamp:3; -webkit-box-orient:vertical; }
    .simulation-category-tags { display:flex; flex-wrap:wrap; gap:.45rem; min-height:2rem; margin-bottom:1rem; }
    .simulation-category-tag { display:inline-flex; align-items:center; gap:.3rem; max-width:100%; padding:.4rem .65rem; border-radius:999px; background:#eff6ff; color:#075985; font-size:.8rem; font-weight:700; line-height:1.2; }
    .survey-card .card-body-pad { padding: 1rem; }
    .survey-card .form-control, .survey-card .form-select { min-height:40px; border-radius:.8rem; border-color:rgba(148,163,184,.28); padding-top:.55rem; padding-bottom:.55rem; }
    .survey-card .form-check { padding:.6rem .85rem .6rem 2rem; border:1px solid rgba(148,163,184,.22); border-radius:.9rem; background:#fff; }
    .survey-card .form-check + .form-check { margin-top:.55rem; }
    .survey-chart-shell { border-radius:1.25rem; background:linear-gradient(180deg,#f8fafc,#eef2ff); border:1px solid rgba(148,163,184,.18); }
    .survey-chart-wrap { max-width: 300px; margin: 0 auto; }
    .survey-chart-wrap canvas { width: 100% !important; height: 260px !important; }
    .survey-question-select-wrap {
        flex: 1 1 320px;
        max-width: 100%;
        min-width: 0;
    }
    #activeSurveyQuestionSelect {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .survey-meta-block { margin-bottom: 1rem !important; }
    .survey-participants-button {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 245px;
        padding: 1.1rem 1.5rem;
        border-radius: 1rem;
        background: linear-gradient(90deg, #a855f7 0%, #7c3aed 42%, #3b82f6 74%, #22d3ee 100%);
        color: #fff;
        box-shadow: 0 16px 34px rgba(124, 58, 237, .24), 0 10px 24px rgba(34, 211, 238, .16);
        text-align: center;
    }
    .survey-participants-button small {
        display: block;
        margin-bottom: .4rem;
        font-size: .78rem;
        font-weight: 600;
        letter-spacing: .04em;
        color: rgba(255,255,255,.88);
    }
    .survey-participants-button strong {
        display: block;
        font-size: clamp(2rem, 2.8vw, 2.7rem);
        line-height: 1;
        font-weight: 300;
        letter-spacing: -.02em;
    }
    .survey-question-block { margin-bottom: 1rem !important; }
    .survey-question-block .form-label { margin-bottom: .75rem !important; }
    .survey-question-block .text-secondary.small { margin-bottom: .5rem !important; }
    .survey-share-panel {
        margin-top: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, rgba(37,99,235,.08), rgba(245,158,11,.12));
        border: 1px solid rgba(37,99,235,.12);
    }
    .survey-share-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .65rem;
        margin-top: .85rem;
    }
    .survey-share-actions .btn {
        min-height: 42px;
    }
    .survey-share-links {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem;
        margin-top: .85rem;
    }
    .survey-share-links a {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .55rem .8rem;
        border-radius: 999px;
        text-decoration: none;
        background: #fff;
        border: 1px solid rgba(148,163,184,.22);
        color: #334155;
        font-size: .9rem;
        font-weight: 700;
    }
    .survey-share-feedback {
        margin-top: .75rem;
        color: #0f766e;
        font-size: .88rem;
        font-weight: 600;
    }
    .empty-state { padding:2rem; border-radius:1.5rem; background:rgba(255,255,255,.7); border:1px dashed rgba(148,163,184,.45); color:#64748b; }
    @media (max-width: 767.98px) {
        .hero-block { padding:1.2rem; border-radius:1.5rem; }
        .hero-news-layout { grid-template-columns:1fr; }
        .hero-carousel .carousel-inner, .hero-carousel .carousel-item, .hero-carousel-slide { height:308px; }
        .hero-main-panel,
        .hero-side-grid { min-height:0; }
        .hero-carousel-content { left:1rem; right:1rem; bottom:1rem; }
        .hero-carousel-title { max-width:100%; font-size:1.35rem; }
        .hero-carousel-copy { max-width:100%; font-size:.9rem; }
        .hero-carousel-nav.hero-carousel-nav-bottom { padding:1rem 1rem 1.05rem; }
        .hero-side-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:1rem; padding-top:0; }
        .hero-side-card img, .hero-side-placeholder { height:120px; }
        .hero-title { max-width:none; }
        .hero-story img { height:220px; }
        .feature-slide { min-height:360px; }
        .feature-slide-content { padding:1.35rem; }
        .mini-story { display:block; }
        .mini-story img {
            width:100%;
            height:140px;
            flex:auto;
            margin-bottom:.85rem;
        }
        .mini-story-body { padding:0; }
        .story-image { aspect-ratio:16/10; }
        .story-card .story-image {
            aspect-ratio: auto;
            height: 170px;
        }
        .games-promo-card {
            grid-template-columns:1fr;
        }
        .games-promo-action {
            width:100%;
        }
        .survey-chart-shell { padding: 1rem !important; }
        .survey-chart-wrap { max-width: 220px; }
        .survey-chart-wrap canvas { height: 180px !important; }
        .survey-question-select-wrap { flex-basis: 100%; }
        .survey-participants-button {
            width: 100%;
            min-width: 0;
            margin-top: .85rem;
        }
    }
    @media (max-width: 1199.98px) {
        .hero-news-layout { grid-template-columns:1fr; }
        .hero-side-grid { padding-top:0; }
    }
    @media (max-width: 575.98px) {
        .home-shell { padding-top:1rem !important; padding-bottom:1.5rem !important; }
        .home-shell > .container { padding-left:.85rem; padding-right:.85rem; }
        .hero-side-grid { grid-template-columns:1fr; }
        .hero-side-card img, .hero-side-placeholder { height:200px; }
        .survey-card .card-body-pad { padding: .85rem; }
        .survey-question-block { margin-bottom: .85rem !important; }
        .survey-share-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="home-shell py-4 py-lg-5">
    <div class="container">
        @include('partials.adsense.ad-unit', [
            'slot' => 'home_top',
            'class' => 'mx-auto',
            'style' => 'max-width: 1100px;',
            'insStyle' => 'display:block; text-align:center;',
            'layout' => 'in-article',
            'format' => 'fluid',
            'label' => null,
        ])

        <section class="section-block pt-0">
            @if($carouselBlogs->isNotEmpty())
                <div class="hero-news-layout">
                    <div class="hero-main-panel">
                        <div id="homeHeroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="4200" data-bs-pause="hover">
                            <div class="carousel-inner">
                                @foreach($carouselBlogs as $index => $item)
                                    @php($heroCover = $item->cover_image ? route('blog.media.show', ['path' => $item->cover_image]) : null)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <a href="{{ route('blog.public.show', $item) }}" class="hero-carousel-slide">
                                            @if($heroCover)
                                                <img src="{{ $heroCover }}" alt="{{ $item->title }}" width="1600" height="900" @if($index === 0) fetchpriority="high" @endif>
                                            @else
                                                <div class="hero-side-placeholder h-100 d-grid place-items-center"><i class="fa fa-newspaper-o fa-3x text-secondary"></i></div>
                                            @endif
                                            <div class="hero-carousel-content">
                                                <h1 class="hero-carousel-title">{{ Str::limit($item->title, 62) }}</h1>
                                                <p class="hero-carousel-copy">{{ Str::limit(strip_tags($item->content), 72) }}</p>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                            <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span></button>
                            <button class="carousel-control-next" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span></button>
                        </div>

                        <div class="hero-carousel-nav hero-carousel-nav-bottom">
                            @foreach($carouselBlogs as $index => $item)
                                <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}">{{ $index + 1 }}</button>
                            @endforeach
                            <a href="{{ route('blog.public.index') }}" class="hero-all-link">Tümü</a>
                        </div>
                    </div>

                    <div class="hero-side-grid">
                        @foreach($recentBlogs->take(4) as $item)
                            @php($sideCover = $item->cover_image ? route('blog.media.show', ['path' => $item->cover_image]) : null)
                            <a href="{{ route('blog.public.show', $item) }}" class="hero-side-card">
                                @if($sideCover)
                                    <img src="{{ $sideCover }}" alt="{{ $item->title }}" width="640" height="360">
                                @else
                                    <div class="hero-side-placeholder d-grid place-items-center"><i class="fa fa-file-text-o text-secondary"></i></div>
                                @endif
                                <span class="hero-side-title">{{ Str::limit($item->title, 48) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="hero-carousel-empty">
                    <div>
                        <h1 class="hero-carousel-title">Bilgiyi görünür hale getir.</h1>
                        <p class="hero-carousel-copy">İlk içerikler geldiğinde ana sayfa karoseli otomatik olarak dolacak.</p>
                    </div>
                </div>
            @endif

            {{-- <div class="hero-block d-none">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7">
                        <span class="section-kicker"><i class="fa fa-compass"></i>  Tariyer ve öğrenme merkezi</span>
                        <h1 class="hero-title">Bilgiyi görünür, kariyeri erişilebilir hale getir.</h1>
                        <p class="hero-copy mb-4">
                            Güncel içerikler, sınav hazırlığı, anketler, CV araçları ve Topluluk akışı tek ana sayfada birleşiyor.
                        </p>
                        <div class="hero-actions d-flex flex-wrap gap-3 mb-4">
                            <a href="{{ route('blog.public.index') }}" class="btn btn-warning text-dark rounded-pill">Yazıları Keşfet <i class="fa fa-arrow-right ms-2"></i></a>
                            <a href="{{ route('sinav.lessons.index') }}" class="btn btn-outline-light rounded-pill">Soru Platformu</a>
                            <a href="{{ route('cv.create') }}" class="btn btn-light rounded-pill">CV Oluştur</a>
                        </div>
                        <div class="d-flex flex-wrap gap-4">
                            <div><span class="hero-stat-value">{{ $recentBlogs->count() }}</span><span class="hero-stat-label">öne çıkan içerik</span></div>
                            <div><span class="hero-stat-value">{{ $menus->count() }}</span><span class="hero-stat-label">kategori başlığı</span></div>
                            <div><span class="hero-stat-value">{{ $lessons->count() }}</span><span class="hero-stat-label">aktif ders alanı</span></div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        @if($latestBlog)
                            @php($latestCover = $latestBlog->cover_image ? route('blog.media.show', ['path' => $latestBlog->cover_image]) : null)
                            <article class="hero-story">
                                @if($latestCover)
                                    <img src="{{ $latestCover }}" alt="{{ $latestBlog->title }}" width="1600" height="900">
                                @else
                                    <div class="d-grid place-items-center" style="height:300px;background:linear-gradient(135deg,rgba(255,255,255,.16),rgba(255,255,255,.06));">
                                        <i class="fa fa-newspaper-o fa-3x text-white"></i>
                                    </div>
                                @endif
                                <div class="hero-story-body">
                                    <div class="eyebrow eyebrow-light mb-2">{{ $latestBlog->category->name ?? 'Genel içerik' }}</div>
                                    <h2 class="h3 fw-bold mb-3">{{ $latestBlog->title }}</h2>
                                    <p class="mb-3 text-white-50">{{ Str::limit(strip_tags($latestBlog->content), 140) }}</p>
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <span class="small text-white-50">{{ optional($latestBlog->created_at)->format('d.m.Y') }}</span>
                                        <a href="{{ route('blog.public.show', $latestBlog) }}" class="btn btn-sm btn-warning text-dark rounded-pill px-3">Devamını Oku</a>
                                    </div>
                                </div>
                            </article>
                        @else
                            <div class="hero-story p-4 h-100 d-flex align-items-center">
                                <div>
                                    <div class="eyebrow eyebrow-light mb-2">Hazırlanıyor</div>
                                    <h2 class="h3 fw-bold mb-3">Henüz yayınlanmış içerik bulunmuyor.</h2>
                                    <p class="text-white-50 mb-0">İlk içerikler geldiğinde bu alan otomatik olarak öne çıkan hikâyeyi gösterecek.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div> --}}
        </section>


        <section class="section-block">
            <a href="{{ route('game.play') }}" class="games-promo-card">
                <span class="games-promo-content">
                    <span class="games-promo-icon"><i class="fa fa-gamepad"></i></span>
                    <span>
                        <h3>Oyunlar</h3>
                        <p>Giris yapmadan oyun sec, sol menuden istedigin oyunu ac ve hemen oyna.</p>
                    </span>
                </span>
                <span class="games-promo-action">Oyunlara git <i class="fa fa-arrow-right"></i></span>
            </a>

            <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
                <div>
                    <span class="section-kicker"><i class="fa fa-sun-o"></i> Bugün öne çıkanlar</span>
                    <h2 class="section-title">Son eklenen simülasyonlar.</h2>

                </div>
                <a href="{{ route('simulation.index') }}" class="btn btn-outline-dark rounded-pill px-4">Tüm simülasyonlar</a>
            </div>

            @if($latestSimulations->isNotEmpty())
                <div class="row g-4">
                    @foreach($latestSimulations as $item)
                        @php($cover = $item->cover_image_url)
                        <div class="col-12 col-md-6 col-xl-3">
                            <a href="{{ route('simulation.show', $item->slug) }}" class="story-card glass-card h-100 overflow-hidden d-block text-decoration-none">
                                @if($cover)
                                    <img src="{{ $cover }}" alt="{{ $item->title }}" class="story-image" width="1200" height="675">
                                @else
                                    <div class="story-image d-grid place-items-center"><i class="fa fa-flask fa-3x text-secondary"></i></div>
                                @endif
                                <div class="story-body">
                                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3 story-meta">
                                        <span>{{ $item->category->name ?? 'Genel' }}</span>
                                        <span>{{ optional($item->published_at ?? $item->created_at)->format('d.m.Y') }}</span>
                                    </div>
                                    <span class="story-link d-block mb-3">{{ Str::limit($item->title, 62) }}</span>
                                    <p class="text-secondary story-excerpt mb-3">{{ Str::limit(strip_tags($item->excerpt ?: $item->content), 72) }}</p>
                                    <span class="btn btn-sm btn-outline-primary rounded-pill px-3">Aç</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">Henüz yayınlanmış simülasyon bulunmuyor.</div>
            @endif
        </section>

        <section class="section-block">
            <div class="row g-4 align-items-stretch">
                  <h2 class="section-title mt-0">Öne çıkan içerik akışı</h2>
                <div class="col-lg-8">
                    {{-- @if($carouselBlogs->isNotEmpty())
                        <div id="homeStoryCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                @foreach($carouselBlogs as $index => $item)
                                    <button type="button" data-bs-target="#homeStoryCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-label="Slide {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach($carouselBlogs as $index => $item)
                                    @php($slideCover = $item->cover_image ? route('blog.media.show', ['path' => $item->cover_image]) : null)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <article class="feature-slide" @if($slideCover) style="background-image:url('{{ $slideCover }}')" @endif>
                                            <div class="feature-slide-content">
                                                <div class="eyebrow eyebrow-light">{{ $item->category->name ?? 'Genel' }}</div>
                                                <h3 class="feature-slide-title">{{ $item->title }}</h3>
                                                <p class="feature-slide-copy mb-4">{{ Str::limit(strip_tags($item->content), 165) }}</p>
                                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                                    <span class="small text-white-50">{{ optional($item->created_at)->format('d.m.Y') }}</span>
                                                    <a href="{{ route('blog.public.show', $item) }}" class="btn btn-warning text-dark rounded-pill px-4">Oku</a>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#homeStoryCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span></button>
                            <button class="carousel-control-next" type="button" data-bs-target="#homeStoryCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span></button>
                        </div>
                    @else
                        <div class="empty-state h-100">Vitrin için yeterli yayın henüz oluşmadı.</div>
                    @endif --}}
                    @if($latestBlog)
                        @php($featureCover = $latestBlog->cover_image ? route('blog.media.show', ['path' => $latestBlog->cover_image]) : null)
                        <article class="feature-slide" @if($featureCover) style="background-image:url('{{ $featureCover }}')" @endif>
                            <div class="feature-slide-content">
                                <div class="eyebrow eyebrow-light">{{ $latestBlog->category->name ?? 'Genel' }}</div>
                                <h3 class="feature-slide-title">{{ $latestBlog->title }}</h3>
                                <p class="feature-slide-copy mb-4">{{ Str::limit(strip_tags($latestBlog->content), 165) }}</p>
                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                    <span class="small text-white-50">{{ optional($latestBlog->created_at)->format('d.m.Y') }}</span>
                                    <a href="{{ route('blog.public.show', $latestBlog) }}" class="btn btn-warning text-dark rounded-pill px-4">Oku</a>
                                </div>
                            </div>
                        </article>
                    @else
                        <div class="empty-state h-100">Vitrin iÃ§in yeterli yayÄ±n henÃ¼z oluÅŸmadÄ±.</div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <article class="category-card glass-card h-100">
                        <div class="card-body-pad">
                            <div class="eyebrow mb-2">Kategoriler</div>
                            <h3 class="h4 fw-bold mb-3">İçerik Konuları</h3>
                            @forelse($menus as $category)
                                <div class="category-item">
                                    <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                                        <div class="fw-bold">{{ $category->name }}</div>
                                        <span class="badge text-bg-light">{{ $category->blogs_count }}</span>
                                    </div>
                                    @if($category->children->isNotEmpty())
                                        <div>
                                            @foreach($category->children as $child)
                                                <span class="category-pill">{{ $child->name }} ({{ $child->blogs_count }})</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-secondary small mb-0">Alt kategori bulunmuyor.</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-secondary mb-0">Henüz kategori tanımlı değil.</p>
                            @endforelse
                            <a href="{{ route('blog.public.index') }}" class="btn btn-outline-dark rounded-pill w-100 mt-4">Tüm yazılar</a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        @if($subBlogs->isNotEmpty())
            <section class="section-block pt-0">
                <div class="row g-4">
                    @foreach($subBlogs as $item)
                        @php($miniCover = $item->cover_image ? route('blog.media.show', ['path' => $item->cover_image]) : null)
                        <div class="col-12 col-lg-4">
                            <article class="mini-story glass-card h-100">
                                @if($miniCover)
                                    <img src="{{ $miniCover }}" alt="{{ $item->title }}" width="640" height="640">
                                @endif
                                <div class="mini-story-body">
                                    <div class="eyebrow mb-2">{{ $item->category->name ?? 'Genel' }}</div>
                                    <a href="{{ route('blog.public.show', $item) }}" class="story-link d-block mb-2">{{ Str::limit($item->title, 58) }}</a>
                                    <p class="mini-meta mb-0">{{ Str::limit(strip_tags($item->content), 88) }}</p>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="section-block">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                        <div>
                            <span class="section-kicker"><i class="fa fa-book"></i> Sınav hazırlık</span>
                            <h2 class="section-title mb-0">Aktif ders alanları ve konu başlıkları</h2>
                        </div>
                        <a href="{{ route('sinav.lessons.index') }}" class="btn btn-outline-dark rounded-pill px-4">Tüm dersler</a>
                    </div>

                    @if($lessons->isNotEmpty())
                        <div class="row g-4">
                            @foreach($lessons->take(4) as $lesson)
                                <div class="col-md-6">
                                    <article class="lesson-card glass-card h-100">
                                        <div class="card-body-pad">
                                            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                                <div>
                                                    <div class="eyebrow mb-2">Aktif ders</div>
                                                    <h3 class="lesson-title h5 mb-0">{{ $lesson->name }}</h3>
                                                </div>
                                                <span class="badge text-bg-light">{{ $lesson->topics->count() }} konu</span>
                                            </div>
                                            <p class="text-secondary small mb-3">
                                                {{ $lesson->description ? Str::limit($lesson->description, 110) : 'Bu ders için konu ve test içerikleri sınav modülünden yönetilir.' }}
                                            </p>
                                            <div class="mb-4">
                                                @forelse($lesson->topics->take(5) as $topic)
                                                    <span class="topic-pill">
                                                        <i class="fa fa-file-text-o"></i>
                                                        {{ Str::limit($topic->title, 28) }}
                                                        @if($topic->tests_count)
                                                            <strong>{{ $topic->tests_count }}</strong>
                                                        @endif
                                                    </span>
                                                @empty
                                                    <span class="text-secondary small">Henüz aktif konu eklenmemiş.</span>
                                                @endforelse
                                            </div>
                                            <a href="{{ route('sinav.lessons.show', $lesson) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Dersi Aç</a>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">Henüz yayınlanmış aktif ders bulunmuyor.</div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                        <div>
                            <span class="section-kicker"><i class="fa fa-clock-o"></i> Son eklenenler</span>
                            <h2 class="h3 fw-bold mb-0">Hızlı bakış</h2>
                        </div>
                    </div>

                    <div class="d-grid gap-3">
                        @forelse($recentBlogs as $recent)
                            @php($recentCover = $recent->cover_image ? route('blog.media.show', ['path' => $recent->cover_image]) : null)
                            <article class="mini-story glass-card">
                                @if($recentCover)
                                    <img src="{{ $recentCover }}" alt="{{ $recent->title }}" width="512" height="512">
                                @else
                                    <div class="d-grid place-items-center" style="width:96px;height:96px;flex:0 0 96px;background:linear-gradient(135deg,#dbeafe,#e2e8f0);border-radius:1rem;">
                                        <i class="fa fa-file-text-o fa-2x text-secondary"></i>
                                    </div>
                                @endif
                                <div class="mini-story-body">
                                    <div class="mini-meta mb-2">{{ $recent->category->name ?? 'Genel' }} · {{ optional($recent->created_at)->format('d.m.Y') }}</div>
                                    <a href="{{ route('blog.public.show', $recent) }}" class="story-link d-block mb-2">{{ Str::limit($recent->title, 68) }}</a>
                                    <p class="text-secondary small mb-3">{{ Str::limit(strip_tags($recent->content), 86) }}</p>
                                    <a href="{{ route('blog.public.show', $recent) }}" class="mini-story-cta">Aç <i class="fa fa-arrow-right"></i></a>
                                </div>
                            </article>
                        @empty
                            <div class="empty-state">Son eklenen içerikler burada listelenecek.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <section class="section-block">
            <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
                <div>
                    <span class="section-kicker"><i class="fa fa-flask"></i> Simülasyon kategorileri</span>
                    <h2 class="section-title">Simülasyon kategorileri</h2>
                </div>
                <a href="{{ route('simulation.index') }}" class="btn btn-outline-dark rounded-pill px-4">Tüm simülasyonlar</a>
            </div>

            @if($simulationCategories->isNotEmpty())
                <div class="row g-4">
                    @foreach($simulationCategories as $category)
                        <div class="col-md-6 col-xl-3">
                            <article class="module-card simulation-category-card glass-card h-100">
                                <div class="card-body-pad">
                                    <span class="module-icon module-blue">
                                        <i class="fa {{ $category->icon ?: 'fa-flask' }}"></i>
                                    </span>
                                    <a href="{{ route('simulation.category', $category) }}" class="module-title h5">
                                        {{ $category->name }}
                                    </a>
                                    <p class="text-secondary simulation-category-description">
                                        {{ $category->description ?: 'Bu kategori altındaki etkileşimli simülasyonları keşfedin.' }}
                                    </p>
                                    <div class="simulation-category-tags">
                                        @forelse($category->children->take(3) as $child)
                                            <span class="simulation-category-tag">{{ $child->name }}</span>
                                        @empty
                                            <span class="simulation-category-tag">{{ $category->simulations_count }} simülasyon</span>
                                        @endforelse
                                    </div>
                                    <a href="{{ route('simulation.category', $category) }}" class="btn btn-outline-primary rounded-pill px-3">
                                        Kategoriyi Aç
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">Simülasyon kategorileri yayınlandığında bu alanda listelenecek.</div>
            @endif
        </section>

        @include('partials.adsense.ad-unit', [
            'slot' => 'home_bottom',
            'class' => 'mx-auto',
            'style' => 'max-width: 1100px;',
            'insStyle' => 'display:block; text-align:center;',
            'layout' => 'in-article',
            'format' => 'fluid',
            'label' => null,
        ])

        <section class="section-block" id="active-survey">
            <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
                <div>
                    <span class="section-kicker"><i class="fa fa-bar-chart"></i> Aktif anket</span>


                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('survey.public.index') }}" class="btn btn-outline-dark rounded-pill px-4">TÜM ANKETLER</a>
                    @if($activeSurvey)
                        <a href="{{ route('survey.public.show', $activeSurvey) }}" class="btn btn-primary rounded-pill px-4">ANKETE GİT</a>
                    @endif
                </div>
            </div>

            @if($activeSurvey)
                <div class="survey-card glass-card">
                    <div class="card-body-pad">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="survey-meta-block">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="survey-stat"><i class="fa fa-bullseye"></i> Yayında</span>
                                            <span class="survey-stat"><i class="fa fa-globe"></i> {{ $activeSurvey->is_public ? 'Herkese açık' : 'Sınırlı erişim' }}</span>
                                        </div>
                                        <div class="survey-participants-button">
                                            <small>Katılımcı Sayısı</small>
                                            <strong>{{ $activeResponseCount }}</strong>
                                        </div>
                                    </div>
                                    <h3 class="h3 fw-bold mb-2">{{ $activeSurvey->title }}</h3>
                                    @if($activeSurvey->description)
                                        <p class="text-secondary mb-0">{{ $activeSurvey->description }}</p>
                                    @endif
                                </div>

                                <div class="survey-share-panel">
                                    <div class="fw-bold">Anketi paylaş</div>

                                    <div class="survey-share-actions">
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary rounded-pill px-4"
                                            data-survey-share
                                            data-share-title="{{ $activeSurvey->title }}"
                                            data-share-text="{{ 'Bu ankete katıl: '.$activeSurvey->title }}"
                                            data-share-url="{{ route('survey.public.show', $activeSurvey) }}"
                                        >
                                            <i class="fa fa-share-alt me-2"></i>Anketi Paylaş
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-light rounded-pill px-4 border"
                                            data-copy-share-link
                                            data-share-url="{{ route('survey.public.show', $activeSurvey) }}"
                                        >
                                            <i class="fa fa-link me-2"></i>Linki Kopyala
                                        </button>
                                    </div>
                                    <div class="survey-share-links">
                                        <a href="https://wa.me/?text={{ rawurlencode('Bu ankete katıl: '.$activeSurvey->title.' '.route('survey.public.show', $activeSurvey)) }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i>WhatsApp</a>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(route('survey.public.show', $activeSurvey)) }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i>Facebook</a>
                                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ rawurlencode(route('survey.public.show', $activeSurvey)) }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-linkedin-in"></i>LinkedIn</a>

                                    </div>
                                    <div class="survey-share-feedback d-none" data-share-feedback></div>
                                </div>

                                <form id="survey-form-{{ $activeSurvey->id }}" class="survey-ajax-form" data-submit-url="{{ route('survey.public.submit', $activeSurvey) }}">
                                    @include('partials.bot-protection')
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6"><input type="text" name="participant_name" class="form-control" placeholder="İsim (isteğe bağlı)"></div>
                                        <div class="col-md-6"><input type="email" name="participant_email" class="form-control" placeholder="E-posta (isteğe bağlı)"></div>
                                    </div>

                                    @foreach($activeSurvey->questions as $question)
                                        <div class="survey-question-block" data-question-id="{{ $question->id }}" data-question-type="{{ $question->type }}">
                                            <label class="form-label fw-semibold d-block mb-3">
                                                {{ $question->question }}
                                                @if($question->is_required)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            @if($question->help_text)
                                                <div class="text-secondary small mb-3">{{ $question->help_text }}</div>
                                            @endif

                                            @if($question->type === 'text')
                                                <textarea name="question_{{ $question->id }}" rows="4" class="form-control" placeholder="Cevabınızı yazın"></textarea>
                                            @elseif($question->type === 'multiple_choice')
                                                @foreach($question->options as $option)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="question_{{ $question->id }}[]" value="{{ $option->id }}" id="question_{{ $question->id }}_option_{{ $option->id }}">
                                                        <label class="form-check-label" for="question_{{ $question->id }}_option_{{ $option->id }}">{{ $option->label }}</label>
                                                    </div>
                                                @endforeach
                                            @else
                                                @foreach($question->options as $option)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="question_{{ $question->id }}" value="{{ $option->id }}" id="question_{{ $question->id }}_option_{{ $option->id }}">
                                                        <label class="form-check-label" for="question_{{ $question->id }}_option_{{ $option->id }}">{{ $option->label }}</label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endforeach

                                    <div class="alert alert-danger d-none" id="survey-form-{{ $activeSurvey->id }}-alert" data-survey-alert></div>
                                    <button class="btn btn-primary rounded-pill px-4 py-2" type="submit">Oyumu Gönder</button>
                                </form>
                            </div>

                            <div class="col-lg-6">
                                <div class="survey-chart-shell p-3 h-100">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                                        <div>
                                            <div class="eyebrow mb-2">Canlı dağılım</div>
                                            <h3 class="h5 fw-bold mb-0">Katılım oranları</h3>
                                        </div>
                                        <div class="survey-question-select-wrap">
                                            <select class="form-select form-select-sm" id="activeSurveyQuestionSelect"></select>
                                        </div>
                                    </div>
                                    <p class="text-secondary small mb-3">Seçilen soruya göre seçenek bazlı dağılım burada gösterilir.</p>
                                    <div class="survey-chart-wrap">
                                        <canvas id="activeSurveyChart"></canvas>
                                    </div>
                                    <div class="small text-muted mt-3 d-none" id="activeSurveyChartEmpty">Grafik için seçenekli soru bulunamadı.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-state">Aktif bir anket bulunmuyor. Yeni anket yayınlandığında bu alan otomatik olarak form ve grafik görünümüne dönüşecek.</div>
            @endif
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const heroCarousel = document.getElementById('homeHeroCarousel');
        const heroNavButtons = document.querySelectorAll('.hero-carousel-nav [data-bs-slide-to]');
        const shareButtons = document.querySelectorAll('[data-survey-share]');
        const copyButtons = document.querySelectorAll('[data-copy-share-link]');

        if (heroCarousel && heroNavButtons.length) {
            heroCarousel.addEventListener('slide.bs.carousel', (event) => {
                heroNavButtons.forEach((button) => {
                    const isActive = Number(button.getAttribute('data-bs-slide-to')) === event.to;
                    button.classList.toggle('active', isActive);
                    button.setAttribute('aria-current', isActive ? 'true' : 'false');
                });
            });
        }

        async function copyShareLink(url, feedbackNode) {
            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(url);
                } else {
                    const tempInput = document.createElement('input');
                    tempInput.value = url;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    tempInput.remove();
                }

                if (feedbackNode) {
                    feedbackNode.textContent = 'Paylaşım linki kopyalandı.';
                    feedbackNode.classList.remove('d-none');
                }
            } catch (error) {
                if (feedbackNode) {
                    feedbackNode.textContent = 'Link kopyalanamadı. Sosyal ağ butonlarını kullanabilirsiniz.';
                    feedbackNode.classList.remove('d-none');
                }
            }
        }

        shareButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const wrapper = button.closest('.survey-share-panel');
                const feedbackNode = wrapper?.querySelector('[data-share-feedback]');
                const sharePayload = {
                    title: button.dataset.shareTitle || document.title,
                    text: button.dataset.shareText || document.title,
                    url: button.dataset.shareUrl || window.location.href,
                };

                if (feedbackNode) {
                    feedbackNode.classList.add('d-none');
                }

                try {
                    if (navigator.share) {
                        await navigator.share(sharePayload);
                        if (feedbackNode) {
                            feedbackNode.textContent = 'Anket paylaşımı açıldı.';
                            feedbackNode.classList.remove('d-none');
                        }
                        return;
                    }
                } catch (error) {
                    if (error?.name === 'AbortError') {
                        return;
                    }
                }

                await copyShareLink(sharePayload.url, feedbackNode);
            });
        });

        copyButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const wrapper = button.closest('.survey-share-panel');
                const feedbackNode = wrapper?.querySelector('[data-share-feedback]');

                if (feedbackNode) {
                    feedbackNode.classList.add('d-none');
                }

                await copyShareLink(button.dataset.shareUrl || window.location.href, feedbackNode);
            });
        });

        const activeStats = @json($activeStats ?? []);
        const activeSelect = document.getElementById('activeSurveyQuestionSelect');
        const activeCanvas = document.getElementById('activeSurveyChart');
        const activeEmpty = document.getElementById('activeSurveyChartEmpty');
        let activeChart;

        if (!activeCanvas || !activeSelect || !window.Chart) {
            return;
        }

        const optionStats = activeStats.filter((stat) => Array.isArray(stat.options) && stat.options.length);

        if (!optionStats.length) {
            if (activeEmpty) activeEmpty.classList.remove('d-none');
            return;
        }

        optionStats.forEach((stat) => {
            const option = document.createElement('option');
            option.value = stat.id;
            option.textContent = stat.question;
            activeSelect.appendChild(option);
        });

        function renderChart(questionId) {
            const stat = optionStats.find((item) => String(item.id) === String(questionId)) || optionStats[0];
            if (!stat) return;

            if (activeChart) {
                activeChart.destroy();
            }

            activeChart = new Chart(activeCanvas, {
                type: 'doughnut',
                data: {
                    labels: stat.options.map((option) => option.label),
                    datasets: [{
                        data: stat.options.map((option) => option.count),
                        backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6', '#f97316'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    cutout: '62%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            });
        }

        activeSelect.addEventListener('change', (event) => renderChart(event.target.value));
        renderChart(activeSelect.options[0].value);
    });


    /*
    self.addEventListener('fetch', function(event) {

    if (
        event.request.url.includes('googlesyndication.com') ||
        event.request.url.includes('googleads') ||
        event.request.url.includes('doubleclick.net')
    ) {
        return; // cacheleme
    }

    // diğer cache logic
});
    */
</script>
@endpush
