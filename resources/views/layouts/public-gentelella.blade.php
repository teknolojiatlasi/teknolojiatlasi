<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo')
    @include('partials.google-analytics')
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon-star.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon-star.svg') }}">
    <link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/init-modern-C9VwsGcw.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/vendor-forms-Bzw2SBf7.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/choices-DUfNJJYf.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/cropper.esm-BA2OjHm-.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/form_upload-CIgf0F6L.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/leaflet-CIGW-MKW.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/font-awesome-6-5-0.min.css') }}">
    <style>
        @font-face {
            font-family: "Font Awesome 6 Free";
            font-style: normal;
            font-weight: 400;
            font-display: block;
            src: url("{{ asset('vendor/gentelella/fonts/fa-regular-400-DQuI-phE.woff2') }}") format("woff2");
        }

        @font-face {
            font-family: "Font Awesome 6 Free";
            font-style: normal;
            font-weight: 900;
            font-display: block;
            src: url("{{ asset('vendor/gentelella/fonts/fa-solid-900-BLm1ImsD.woff2') }}") format("woff2");
        }

        @font-face {
            font-family: "Font Awesome 6 Brands";
            font-style: normal;
            font-weight: 400;
            font-display: block;
            src: url("{{ asset('vendor/gentelella/fonts/fa-brands-400-BdzBFuGj.woff2') }}") format("woff2");
        }

        .fa,
        .fas,
        .far,
        .fab {
            display: inline-block;
            line-height: 1;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .main_container .right_col { margin-left: 0 !important; }
        .nav-md .container.body .right_col { margin-left: 0 !important; }
        .top_nav .nav_menu { margin-left: 0 !important; }

        .mn-topbar {
            background: #0b5aa6;
            color: #fff;
        }

        .mn-topbar a {
            color: #fff;
            text-decoration: none;
        }

        .mn-topbar a:hover,
        .mn-subbar a:hover {
            text-decoration: underline;
        }

        .mn-topbar .mn-brand {
            font-weight: 700;
            letter-spacing: .2px;
        }

        .mn-topbar .mn-link {
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            opacity: .95;
        }

        .mn-subbar {
            background: #083a6a;
            color: #dbeafe;
            font-size: 12px;
        }

        .mn-subbar a {
            color: #dbeafe;
            text-decoration: none;
        }

        .mn-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 12px;
        }
    </style>
    @stack('styles')
    @laravelPWA
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <header>
            <div class="mn-topbar">
                <div class="mn-page" style="display:flex;align-items:center;gap:16px;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:16px;">
                        <a class="mn-brand" href="{{ url('/') }}">memurlar.net</a>
                        <nav style="display:flex;gap:12px;flex-wrap:wrap;">
                            <a class="mn-link" href="#">Haber</a>
                            <a class="mn-link" href="#">Forum</a>
                            <a class="mn-link" href="{{ url('/ilansayfasi') }}">İlan</a>
                            <a class="mn-link" href="#">KPSS</a>
                            <a class="mn-link" href="#">Sınav</a>
                            <a class="mn-link" href="#">Bi Mola</a>
                            <a class="mn-link" href="#">Maaş</a>
                            <a class="mn-link" href="#">Foto Galeri</a>
                        </nav>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <a href="#" title="Ara"><i class="fa fa-search"></i></a>
                        <a href="#" class="mn-link" style="text-transform:none;"><i class="fa fa-user-plus"></i> Yeni Üye</a>
                        <a href="#" class="mn-link" style="text-transform:none;"><i class="fa fa-user"></i> Üye Girişi</a>
                    </div>
                </div>
            </div>
            <div class="mn-subbar">
                <div class="mn-page" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                    <a href="#">Kamu Personeli</a>
                    <span>•</span>
                    <a href="#">KPSS</a>
                    <span>•</span>
                    <a href="#">Haberler</a>
                    <span>•</span>
                    <a href="#">Mevzuat</a>
                    <span>•</span>
                    <a href="#">Öğretmen</a>
                    <span style="margin-left:auto;opacity:.9;">
                        <i class="fa fa-phone"></i> 0532 540 0 590
                    </span>
                </div>
            </div>
        </header>

        <main class="right_col" role="main" style="padding:0;">
            <div class="mn-page">
                @yield('content')
            </div>
        </main>

        <footer style="padding: 18px 0;">
            <div class="mn-page" style="border-top:1px solid #e5e7eb;padding-top:12px;color:#64748b;font-size:12px;">
                <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div>© {{ now()->year }} memurlar.net</div>
                    <div>Gentelella UI</div>
                </div>
            </div>
        </footer>
    </div>
</div>
@include('partials.pwa-install-prompt')
@include('partials.pwa-push-subscription')

<script type="module" src="{{ asset('vendor/gentelella/js/vendor-core-BeYXbRhn.js') }}"></script>
<script type="module" src="{{ asset('vendor/gentelella/js/vendor-utils-BbL_2bOZ.js') }}"></script>
<script type="module" src="{{ asset('vendor/gentelella/js/vendor-ui-CQXOSmyR.js') }}"></script>
<script type="module" src="{{ asset('vendor/gentelella/js/init-modern-D8X3YKeP.js') }}"></script>
@stack('scripts')
</body>
</html>
