<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        @include('partials.seo')
        @include('partials.google-analytics')
        @include('partials.google-adsense')
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon-star.svg') }}">
        <link rel="shortcut icon" href="{{ asset('favicon-star.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap 5 -->
        <link rel="stylesheet" href="{{ asset('vendor/front/company/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
        @laravelPWA

        {{-- Vite CSS --}}
        {{-- {{ module_vite('build-sinav', 'resources/assets/sass/app.scss') }} --}}
        @stack('styles')
    </head>

    <body>
        <div class="container-fluid my-4">
            {{ $slot }}
        </div>

        {{-- Vite JS --}}
        {{-- {{ module_vite('build-sinav', 'resources/assets/js/app.js') }} --}}
        <script src="{{ asset('vendor/front/company/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
        @stack('scripts')
    </body>
</html>
