
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Yönetim Paneli - Gentelella')</title>
    <meta name="robots" content="noindex, nofollow">
    @include('partials.google-analytics')
    @include('partials.google-adsense')
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon-star.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon-star.svg') }}">
    {{-- Gentelella CSS --}}
<link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/init-modern-C9VwsGcw.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/vendor-forms-Bzw2SBf7.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/choices-DUfNJJYf.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/cropper.esm-BA2OjHm-.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/form_upload-CIgf0F6L.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/leaflet-CIGW-MKW.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/font-awesome-6-5-0.min.css') }}" />

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
    </style>

    @stack('styles')
    @laravelPWA
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">

        {{-- SOL SİDEBAR --}}
        @include('layouts.partials.sidebar')


        {{-- ÜST NAVBAR --}}
        @include('layouts.partials.topnav')
        {{-- SAYFA İÇERİĞİ --}}
        <div class="right_col" role="main">
            @yield('content')
        </div>

        {{-- FOOTER --}}
        <footer>
      <div class="float-end">
        Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com" target="_blank" rel="noopener">Colorlib</a>
      </div>
      <div class="clearfix"></div>
    </footer>


    </div>
</div>
<!-- Ana Gentelella JS -->
<script type="module" src="{{ asset('vendor/gentelella/js/vendor-core-BeYXbRhn.js') }}"></script>
<script type="module" src="{{ asset('vendor/gentelella/js/vendor-utils-BbL_2bOZ.js') }}"></script>
<script type="module" src="{{ asset('vendor/gentelella/js/vendor-ui-CQXOSmyR.js') }}"></script>
<script type="module" src="{{ asset('vendor/gentelella/js/init-modern-D8X3YKeP.js') }}"></script>
@stack('scripts')

</body>
</html>
