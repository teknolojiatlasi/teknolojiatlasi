<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Teknoloji Atlası') }}</title>
        @include('partials.google-analytics')
        @include('partials.google-adsense')
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon-star.svg') }}">
        <link rel="shortcut icon" href="{{ asset('favicon-star.svg') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;instrument+sans:500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @laravelPWA
    </head>
    <body class="antialiased text-slate-900">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,rgba(245,158,11,0.18),transparent_28%),linear-gradient(180deg,#f8fafc_0%,#e0e7ff_100%)]">
            <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid w-full overflow-hidden rounded-[2rem] border border-slate-200/70 bg-white/80 shadow-[0_30px_90px_rgba(15,23,42,0.12)] backdrop-blur xl:grid-cols-[1.05fr_0.95fr]">
                    <section class="relative hidden overflow-hidden bg-slate-950 p-10 text-white xl:flex xl:flex-col xl:justify-between">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(56,189,248,0.28),transparent_24%),radial-gradient(circle_at_bottom_left,rgba(245,158,11,0.22),transparent_24%)]"></div>
                        <div class="relative z-10">
                            <a href="{{ route('anasayfa') }}" class="inline-flex items-center gap-3 text-lg font-bold tracking-tight">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-400 to-blue-600 text-white shadow-lg shadow-blue-900/30">
                                    ★
                                </span>
                                <span>Teknoloji Atlası</span>
                            </a>
                        </div>

                        <div class="relative z-10 max-w-xl">
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                                <div class="text-sm font-semibold uppercase tracking-[0.22em] text-sky-100/90">
                                    Hesap Alani
                                </div>
                                <div class="mt-3 text-3xl font-extrabold leading-tight text-white">
                                    Giris ve kayit islemleri
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="px-5 py-8 sm:px-8 lg:px-10 xl:px-12 xl:py-12">
                        <div class="mb-8 flex items-center justify-between xl:hidden">
                            <a href="{{ route('anasayfa') }}" class="inline-flex items-center gap-3 text-lg font-bold tracking-tight text-slate-900">
                                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-400 to-blue-600 text-white shadow-lg shadow-blue-900/20">
                                    ★
                                </span>
                                <span>Teknoloji Atlası</span>
                            </a>
                        </div>

                        <div class="mx-auto w-full max-w-xl">
                            {{ $slot }}
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>
