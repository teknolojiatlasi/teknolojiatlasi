@extends('layouts.app2')

@section('title', 'Giriş Yap')

@section('content')
@php
    $redirectTarget = $redirect ?? request('redirect');
@endphp
<style>
    .auth-shell {
        padding: 2rem 0 3rem;
    }

    .auth-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.45rem;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.1);
        backdrop-filter: blur(10px);
    }

    .auth-card .card-body {
        padding: 1.7rem;
    }

    .auth-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1.05rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.18);
    }

    .auth-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.42rem 0.8rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.08);
        color: #00fdf6;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .auth-header-title {
        font-size: clamp(1.55rem, 2.4vw, 2rem);
        font-weight: 800;
        line-height: 1.15;
        margin: 0.9rem 0 0.45rem;
        color: #0f172a;
    }

    .auth-header-copy {
        margin: 0;
        color: #64748b;
        line-height: 1.65;
    }

    .auth-input,
    .auth-check {
        border-radius: 1rem;
    }

    .auth-input {
        border-color: rgba(148, 163, 184, 0.28);
        padding: 0.9rem 1rem;
    }

    .auth-input:focus {
        border-color: rgba(37, 99, 235, 0.45);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.12);
    }

    .auth-btn {
        border-radius: 999px;
        padding: 0.9rem 1.1rem;
        font-weight: 700;
    }

    .auth-outline {
        border-radius: 999px;
        padding: 0.8rem 1.1rem;
        font-weight: 700;
    }

    .auth-side-card {
        height: 100%;
        border-radius: 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.95), rgba(241, 245, 249, 0.95));
        padding: 1.35rem;
    }

    .auth-social-grid {
        display: grid;
        gap: 0.75rem;
    }

    .auth-social-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.65rem;
        width: 100%;
        border-radius: 999px;
        padding: 0.82rem 1rem;
        border: 1px solid rgba(148, 163, 184, 0.24);
        background: #fff;
        color: #0f172a;
        font-weight: 700;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .auth-social-btn:hover {
        color: #0f172a;
        transform: translateY(-1px);
        border-color: rgba(59, 130, 246, 0.24);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
    }

    .auth-social-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        color: #fff;
        flex: 0 0 2rem;
    }

    .auth-social-google { background: linear-gradient(135deg, #ea4335, #fbbc05); }
    .auth-social-github { background: linear-gradient(135deg, #111827, #334155); }
    .auth-social-facebook { background: linear-gradient(135deg, #1877f2, #1455c0); }

    @media (max-width: 991.98px) {
        .auth-shell {
            padding-top: 1.5rem;
        }
    }

    @media (max-width: 575.98px) {
        .auth-shell {
            padding: 1.1rem 0 2rem;
        }

        .auth-card {
            border-radius: 1.2rem;
        }

        .auth-card .card-body,
        .auth-side-card {
            padding: 1rem;
        }
    }
</style>

<div class="auth-shell">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="card auth-card border-0">
                    <div class="card-body">
                        <div class="auth-header">
                            <span class="auth-badge"><i class="fa fa-lock"></i> Guvenli Giris</span>
                            <h1 class="auth-header-title">Hesabiniza giris yapin</h1>
                            <p class="auth-header-copy">E-posta adresiniz ve sifrenizle hesabiniza erisin. Giris yaptiktan sonra bulundugunuz sayfaya yonlendirilirsiniz.</p>
                        </div>

                        <div class="row g-4 align-items-start">
                            <div class="col-12 col-lg-7">
                                <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                                    <div>
                                        <h2 class="h3 fw-bold mb-2">Giris Yap</h2>
                                        <p class="text-muted mb-0">E-posta ve sifrenizle hesabiniza baglanin.</p>
                                    </div>
                                    <a href="{{ route('register', array_filter(['redirect' => $redirectTarget])) }}" class="btn btn-outline-primary auth-outline">
                                        Uye Ol
                                    </a>
                                </div>

                                @if (session('status'))
                                    <div class="alert alert-success rounded-4">{{ session('status') }}</div>
                                @endif

                                <form method="POST" action="{{ route('login') }}" class="d-grid gap-3">
                                    @csrf

                                    @if (!empty($redirectTarget))
                                        <input type="hidden" name="redirect" value="{{ $redirectTarget }}">
                                    @endif

                                    <div>
                                        <label for="email" class="form-label fw-semibold">E-posta</label>
                                        <input
                                            id="email"
                                            class="form-control auth-input @error('email') is-invalid @enderror"
                                            type="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            required
                                            autofocus
                                            autocomplete="username"
                                            placeholder="ornek@mail.com"
                                        >
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <label for="password" class="form-label fw-semibold mb-2">Sifre</label>
                                            @if (Route::has('password.request'))
                                                <a class="small fw-semibold text-decoration-none" href="{{ route('password.request') }}">Sifremi unuttum</a>
                                            @endif
                                        </div>
                                        <input
                                            id="password"
                                            class="form-control auth-input @error('password') is-invalid @enderror"
                                            type="password"
                                            name="password"
                                            required
                                            autocomplete="current-password"
                                            placeholder="Sifreni gir"
                                        >
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <label for="remember_me" class="auth-check d-flex align-items-center gap-3 border px-3 py-3 bg-light-subtle">
                                        <input id="remember_me" type="checkbox" class="form-check-input mt-0" name="remember">
                                        <span class="small text-muted">Beni hatirla</span>
                                    </label>

                                    <button type="submit" class="btn btn-primary auth-btn">
                                        Giris Yap
                                    </button>
                                </form>
                            </div>

                            <div class="col-12 col-lg-5">
                                <div class="auth-side-card">
                                    <div class="small fw-bold text-primary text-uppercase mb-2">Yeni Hesap</div>
                                    <h3 class="h4 fw-bold mb-3">Henuz uye degil misin?</h3>
                                    <p class="text-muted mb-4">
                                        Facebook ile saniyeler icinde uye olabilir ya da klasik kayit formunu kullanabilirsin.
                                    </p>

                                    <div class="d-grid gap-2 mb-3">
                                        <a href="{{ route('register', array_filter(['redirect' => $redirectTarget])) }}" class="btn btn-outline-dark auth-outline">
                                            <i class="fa fa-user-plus me-2"></i>Kayit Ol Sayfasina Git
                                        </a>
                                    </div>

                                    <div class="small fw-bold text-uppercase text-muted mb-2">Sosyal giris</div>
                                    <div class="auth-social-grid">
                                        @if (config('services.google.client_id'))
                                            <a href="{{ route('social.redirect', ['provider' => 'google', 'redirect' => $redirectTarget]) }}" class="auth-social-btn">
                                                <span class="auth-social-icon auth-social-google"><i class="fa fa-google"></i></span>
                                                <span>Google ile Giris</span>
                                            </a>
                                        @endif
                                        @if (config('services.github.client_id'))
                                            <a href="{{ route('social.redirect', ['provider' => 'github', 'redirect' => $redirectTarget]) }}" class="auth-social-btn">
                                                <span class="auth-social-icon auth-social-github"><i class="fa fa-github"></i></span>
                                                <span>GitHub ile Giris</span>
                                            </a>
                                        @endif
                                        @if (config('services.facebook.client_id'))
                                            <a href="{{ route('social.redirect', ['provider' => 'facebook', 'redirect' => $redirectTarget]) }}" class="auth-social-btn">
                                                <span class="auth-social-icon auth-social-facebook"><i class="fa fa-facebook"></i></span>
                                                <span>Facebook ile Uye Ol</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
