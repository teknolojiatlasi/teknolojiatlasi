<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo')
    @yield('meta')
    @include('partials.google-analytics')
    @include('partials.google-adsense')
    @if (config('services.turnstile.enabled') && config('services.turnstile.site_key'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon-star.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon-star.svg') }}">
    <link rel="stylesheet" href="{{ asset('vendor/front/company/assets/vendor/bootstrap/css/bootstrap-reboot.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/front/company/assets/vendor/bootstrap/css/bootstrap-grid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/front/company/assets/vendor/bootstrap/css/bootstrap-utilities.min.css') }}">
    <link rel="preload" href="{{ asset('vendor/front/company/assets/vendor/bootstrap/css/bootstrap.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('vendor/front/company/assets/vendor/bootstrap/css/bootstrap.min.css') }}"></noscript>
    <link rel="stylesheet" href="{{ asset('vendor/gentelella/assets/font-awesome-6-5-0.min.css') }}">
    @laravelPWA
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

        :root {
            --page-bg: #f5f7fa;
            --surface-border: rgba(15, 23, 42, 0.08);
            --radius-lg: 1.25rem;
            --radius-md: 1rem;
            --shell-shadow-sm: 0 10px 25px rgba(15, 23, 42, 0.08);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background:
                radial-gradient(circle at top, rgba(250, 204, 21, 0.15), transparent 28%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
            color: #0f172a;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            min-height: 100vh;
        }

        .fa,
        .fab,
        .far,
        .fas {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.15em;
            min-width: 1.15em;
            line-height: 1;
            text-align: center;
            vertical-align: middle;
        }

        .navbar.navbar-blur {
            background: rgba(17, 185, 200, 0.92) !important;

            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
        }

        .navbar {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 0;
            min-height: 76px;
        }

        .navbar-brand {
            color: #fff;
            text-decoration: none;
            font-size: 1.15rem;
        }

        .navbar-toggler {
            padding: 0.5rem 0.75rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(255,255,255,.18);
            background: transparent;
            color: #fff;
        }

        .navbar-toggler-icon {
            display: inline-block;
            width: 1.5rem;
            height: 1.5rem;
            background:
                linear-gradient(#fff, #fff) center 0.32rem / 100% 2px no-repeat,
                linear-gradient(#fff, #fff) center center / 100% 2px no-repeat,
                linear-gradient(#fff, #fff) center calc(100% - 0.32rem) / 100% 2px no-repeat;
        }

        .navbar-collapse {
            flex-basis: 100%;
            flex-grow: 1;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: rgba(255, 255, 255, 0.96);
            text-decoration: none;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border: 1px solid transparent;
            border-radius: 0.85rem;
            padding: 0.7rem 1rem;
            cursor: pointer;
            text-decoration: none;
            line-height: 1.2;
            transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease, border-color .18s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
            box-shadow: var(--shell-shadow-sm);
        }

        .btn-outline-primary {
            background: rgba(255,255,255,.9);
            border-color: rgba(37,99,235,.28);
            color: #00fdf6;
        }

        .btn-outline-dark {
            background: rgba(255,255,255,.9);
            border-color: rgba(15,23,42,.18);
            color: #0f172a;
        }

        .btn-light {
            background: #fff;
            border-color: rgba(148,163,184,.24);
            color: #0f172a;
        }

        .btn-outline-light {
            background: transparent;
            border-color: rgba(255,255,255,.34);
            color: #fff;
        }

        .form-control,
        .form-select {
            display: block;
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 1px solid rgba(148,163,184,.28);
            border-radius: 0.9rem;
            background: #fff;
            color: #0f172a;
        }

        textarea.form-control {
            resize: vertical;
        }

        .form-check {
            position: relative;
        }

        .form-check-input {
            margin-right: 0.45rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.45rem 0.7rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .text-bg-light {
            color: #334155;
            background: #f8fafc;
            border: 1px solid rgba(148,163,184,.2);
        }

        .alert {
            padding: 0.9rem 1rem;
            border-radius: 1rem;
            border: 1px solid transparent;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .card {
            border-radius: var(--radius-md);
            background: #fff;
        }

        .card-img-top {
            border-top-left-radius: var(--radius-md);
            border-top-right-radius: var(--radius-md);
            display: block;
            width: 100%;
            height: auto;
        }

        .card.rounded-4 .card-img-top {
            border-top-left-radius: var(--bs-border-radius-xl);
            border-top-right-radius: var(--bs-border-radius-xl);
        }

        .shadow-sm {
            box-shadow: var(--shell-shadow-sm) !important;
        }

        .rounded-pill {
            border-radius: 999px !important;
        }

        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .footer {
            background:rgb(17 185 200 / 92%) !important;
            color: #cbd5e1;
        }

        .footer-title {
            color: #fff;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer-text {
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .footer-links,
        .footer-contact {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li,
        .footer-contact li {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: #fff;
        }

        .footer-social a {
            display: inline-block;
            margin-right: 10px;
            font-size: 1.2rem;
            color: #cbd5e1;
            transition: color 0.2s;
        }

        .footer-social a:hover {
            color: #f59e0b;
        }

        .footer-divider {
            border-color: rgba(255, 255, 255, 0.1);
            margin: 2rem 0 1rem;
        }

        .footer-bottom {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .navbar-blur {
            backdrop-filter: blur(10px);
            background-color: rgba(15, 23, 42, 0.9) !important;
        }

        .social-strip {
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(10px);
        }

        .social-strip-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.7rem 0;
            min-height: 64px;
        }

        .social-strip-title {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            color: #0f172a;
            font-size: 0.9rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .social-strip-links {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .social-strip-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.9rem;
            border-radius: 999px;
            background: #fff;
            border: 1px solid rgba(148, 163, 184, 0.22);
            color: #0f172a;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            min-height: 42px;
        }

        .social-strip-link:hover {
            color: #0f172a;
            transform: translateY(-1px);
            border-color: rgba(59, 130, 246, 0.28);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .social-strip-link.active {
            color: #0b63ce;
            border-color: rgba(37, 99, 235, 0.26);
            background: rgba(239, 246, 255, 0.92);
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.08);
        }

        .social-strip-link i {
            width: 1.15rem;
            text-align: center;
        }

        .social-strip-label {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.5rem 0.9rem;
            border-radius: 999px;
            background: rgba(239, 246, 255, 0.95);
            border: 1px solid rgba(59, 130, 246, 0.16);
            color: #00fdf6;
            font-size: 0.88rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .social-strip-label.is-child {
            background: #fff;
            border-color: rgba(148, 163, 184, 0.22);
            color: #334155;
            font-weight: 600;
        }

        .navbar .nav-link,
        .navbar-dark .navbar-nav .nav-link,
        .navbar-dark .navbar-nav .show > .nav-link,
        .navbar-dark .navbar-nav .nav-link.active {
            position: relative;
            color: rgba(255, 255, 255, 0.96) !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: #fbbf24 !important;
        }

        .navbar .nav-link.active::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -6px;
            width: 100%;
            height: 2px;
            background-color: #fbbf24;
            border-radius: 999px;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                display: none;
                width: 100%;
                margin-top: 0.85rem;
            }

            .navbar-collapse.show {
                display: block;
            }
        }

        @media (max-width: 767.98px) {
            .social-strip-inner {
                align-items: flex-start;
                flex-direction: column;
            }

            .social-strip-links {
                width: 100%;
                justify-content: flex-start;
            }

            .social-strip-link,
            .social-strip-label {
                font-size: 0.88rem;
                padding: 0.45rem 0.8rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @include('layouts.partials.navbar2')

    <main>
        @yield('content')
    </main>

    @include('layouts.partials.footer2')
    @include('partials.pwa-install-prompt')
    @include('partials.pwa-push-subscription')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loginUrl = @json(route('login'));
            const navbar = document.querySelector('.navbar');
            const toggler = document.querySelector('.navbar-toggler[aria-controls="navMenu"]');
            const mobileMenu = document.getElementById('navMenu');

            const syncMobileMenu = (isOpen) => {
                if (!toggler || !mobileMenu) return;

                mobileMenu.classList.toggle('show', isOpen);
                toggler.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            };

            const closeMobileMenu = () => {
                if (window.innerWidth >= 992) return;
                syncMobileMenu(false);
            };

            if (toggler && mobileMenu) {
                toggler.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    syncMobileMenu(!mobileMenu.classList.contains('show'));
                });

                mobileMenu.querySelectorAll('a[href]').forEach((link) => {
                    link.addEventListener('click', () => {
                        closeMobileMenu();
                    });
                });

                document.addEventListener('click', (event) => {
                    if (window.innerWidth >= 992 || !mobileMenu.classList.contains('show')) {
                        return;
                    }

                    if (navbar && navbar.contains(event.target)) {
                        return;
                    }

                    closeMobileMenu();
                });

                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 992) {
                        syncMobileMenu(false);
                    }
                });
            }

            document.querySelectorAll('a[href]').forEach((link) => {
                try {
                    const url = new URL(link.href, window.location.origin);
                    if (url.origin !== window.location.origin) {
                        return;
                    }
                    if (url.pathname === new URL(loginUrl, window.location.origin).pathname && !url.searchParams.has('redirect')) {
                        url.searchParams.set('redirect', window.location.href);
                        link.href = url.toString();
                    }
                } catch (error) {
                    // Ignore malformed links.
                }
            });

            document.querySelectorAll('img:not([loading])').forEach((img, index) => {
                img.decoding = 'async';

                if (index > 1) {
                    img.loading = 'lazy';
                } else {
                    img.fetchPriority = 'high';
                }
            });

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            window.botProtection = {
                appendToFormData(form, payload) {
                    if (!form || !payload) return;

                    ['_trap', '_started_at', 'cf-turnstile-response'].forEach((name) => {
                        const input = form.querySelector(`[name="${name}"]`);
                        if (!input) return;
                        payload.append(name, input.value || '');
                    });
                },
                appendToObject(form, payload) {
                    if (!form || !payload) return payload;

                    ['_trap', '_started_at', 'cf-turnstile-response'].forEach((name) => {
                        const input = form.querySelector(`[name="${name}"]`);
                        payload[name] = input ? (input.value || '') : '';
                    });

                    return payload;
                },
                reset(form) {
                    if (!form) return;

                    const startedAt = form.querySelector('[name="_started_at"]');
                    if (startedAt) {
                        startedAt.value = String(Math.floor(Date.now() / 1000));
                    }

                    const widget = form.querySelector('.cf-turnstile');
                    if (widget && window.turnstile) {
                        try {
                            window.turnstile.reset(widget);
                        } catch (error) {
                            // Ignore widget reset failures.
                        }
                    }
                },
            };

            document.querySelectorAll('.survey-ajax-form').forEach((form) => {
                const alertBox =
                    form.querySelector('[data-survey-alert]') ||
                    document.getElementById(`${form.id}-alert`);
                const submitUrl = form.dataset.submitUrl;
                const allowRedirect = form.dataset.allowRedirect === 'true';

                if (!submitUrl || !csrf) {
                    return;
                }

                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    if (alertBox) {
                        alertBox.classList.add('d-none');
                    }

                    const payload = {
                        participant_name: form.querySelector('input[name="participant_name"]')?.value,
                        participant_email: form.querySelector('input[name="participant_email"]')?.value,
                        answers: [],
                    };

                    form.querySelectorAll('[data-question-id]').forEach((wrapper) => {
                        const questionId = Number(wrapper.dataset.questionId);
                        const type = wrapper.dataset.questionType;

                        if (type === 'text') {
                            payload.answers.push({
                                survey_question_id: questionId,
                                answer_text: wrapper.querySelector('textarea')?.value || '',
                            });
                            return;
                        }

                        if (type === 'multiple_choice') {
                            payload.answers.push({
                                survey_question_id: questionId,
                                option_ids: Array.from(
                                    wrapper.querySelectorAll('input[type="checkbox"]:checked'),
                                ).map((input) => Number(input.value)),
                            });
                            return;
                        }

                        const selected = wrapper.querySelector('input[type="radio"]:checked');

                        if (selected) {
                            payload.answers.push({
                                survey_question_id: questionId,
                                option_id: Number(selected.value),
                            });
                        }
                    });

                    try {
                        const response = await fetch(submitUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            const messages = data.errors
                                ? Object.values(data.errors).flat().join('<br>')
                                : data.message || 'Gönderim başarısız.';
                            throw new Error(messages);
                        }

                        form.reset();

                        if (alertBox) {
                            alertBox.classList.remove('d-none');
                            alertBox.classList.replace('alert-danger', 'alert-success');
                            alertBox.innerHTML = data.message || 'Yanıtınız kaydedildi.';
                        }

                        if (allowRedirect && data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 600);
                        }
                    } catch (error) {
                        if (!alertBox) {
                            return;
                        }

                        alertBox.classList.remove('d-none');
                        alertBox.classList.replace('alert-success', 'alert-danger');
                        alertBox.innerHTML = error.message;
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('vendor/front/company/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
