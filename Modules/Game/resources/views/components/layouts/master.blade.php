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
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=space-mono:400,700&display=swap" rel="stylesheet" />
        @laravelPWA

        {{-- Vite CSS --}}
        {{-- {{ module_vite('build-game', 'resources/assets/sass/app.scss') }} --}}
    </head>

    <body class="game-pwa-shell">
        {{ $slot }}
        @include('partials.pwa-install-prompt')

        {{-- Vite JS --}}
        {{-- {{ module_vite('build-game', 'resources/assets/js/app.js') }} --}}
    </body>
</html>
