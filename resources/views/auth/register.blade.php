@extends('layouts.app2')

@section('title', 'Kayıt Ol')

@section('content')
<style>
    .auth-shell {
        padding: 3.5rem 0 4.5rem;
    }

    .auth-hero {
        position: relative;
        overflow: hidden;
        border-radius: 1.9rem;
        background:
            radial-gradient(circle at top right, rgba(56, 189, 248, 0.2), transparent 26%),
            radial-gradient(circle at bottom left, rgba(245, 158, 11, 0.16), transparent 28%),
            linear-gradient(135deg, #0f172a 0%, #00fdf6 60%, #2563eb 100%);
        color: #fff;
        padding: 2rem;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.16);
    }

    .auth-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.85rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .auth-title {
        font-size: clamp(2rem, 4vw, 3.3rem);
        line-height: 1.03;
        font-weight: 800;
        margin: 1rem 0 0.8rem;
    }

    .auth-copy {
        max-width: 56ch;
        color: rgba(255, 255, 255, 0.82);
        font-size: 1rem;
        line-height: 1.7;
        margin-bottom: 0;
    }

    .auth-card {
        margin-top: 1.5rem;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.7rem;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.1);
        backdrop-filter: blur(10px);
    }

    .auth-card .card-body {
        padding: 1.7rem;
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

    .auth-meta-card {
        height: 100%;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 1.2rem;
        background: rgba(255, 255, 255, 0.08);
        padding: 1rem;
    }

    .auth-meta-card strong {
        display: block;
        font-size: 1.05rem;
        margin-bottom: 0.25rem;
    }

    .auth-meta-card span {
        color: rgba(255, 255, 255, 0.76);
        font-size: 0.9rem;
        line-height: 1.5;
    }

    @media (max-width: 991.98px) {
        .auth-shell {
            padding-top: 2rem;
        }

        .auth-card {
            margin-top: 1.25rem;
        }
    }
</style>

<div class="auth-shell">
    <div class="container">
        <section class="auth-hero">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-8">
                    <span class="auth-kicker"><i class="fa fa-user-plus"></i> Yeni Hesap</span>
                    <h1 class="auth-title">Kayıt ol, hesabını hemen kullanmaya başla.</h1>
                    <p class="auth-copy">
                        Sosial medya alanına katıl, sınav sonuçlarını kaydet ve kişisel araçlarını tek hesap altında yönet.
                    </p>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="auth-meta-card">
                                <strong>Member Rolü</strong>
                                <span>Kayıt sonrası kullanıcı hesabın otomatik member olarak açılır.</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="auth-meta-card">
                                <strong>Hızlı Başlangıç</strong>
                                <span>Kayıt olduktan sonra sosyal, sınav ve araç alanlarını hemen kullanabilirsin.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="card auth-card border-0">
                    <div class="card-body">
                        <div class="row g-4 align-items-start">
                            <div class="col-12 col-lg-7">
                                <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                                    <div>
                                        <h2 class="h3 fw-bold mb-2">Kayıt Ol</h2>
                                        <p class="text-muted mb-0">Yeni hesabını oluşturarak sisteme katıl.</p>
                                    </div>
                                    <a href="{{ route('login', array_filter(['redirect' => request('redirect')])) }}" class="btn btn-outline-primary auth-outline">
                                        Giriş Yap
                                    </a>
                                </div>

                                <form method="POST" action="{{ route('register') }}" class="d-grid gap-3">
                                    @csrf

                                    @if (request('redirect'))
                                        <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                                    @endif

                                    <div>
                                        <label for="name" class="form-label fw-semibold">Ad Soyad</label>
                                        <input
                                            id="name"
                                            class="form-control auth-input @error('name') is-invalid @enderror"
                                            type="text"
                                            name="name"
                                            value="{{ old('name') }}"
                                            required
                                            autofocus
                                            autocomplete="name"
                                            placeholder="Adın ve soyadın"
                                        >
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="email" class="form-label fw-semibold">E-posta</label>
                                        <input
                                            id="email"
                                            class="form-control auth-input @error('email') is-invalid @enderror"
                                            type="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            required
                                            autocomplete="username"
                                            placeholder="ornek@mail.com"
                                        >
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password" class="form-label fw-semibold">Şifre</label>
                                        <input
                                            id="password"
                                            class="form-control auth-input @error('password') is-invalid @enderror"
                                            type="password"
                                            name="password"
                                            required
                                            autocomplete="new-password"
                                            placeholder="Güçlü bir şifre belirle"
                                        >
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation" class="form-label fw-semibold">Şifre Tekrar</label>
                                        <input
                                            id="password_confirmation"
                                            class="form-control auth-input @error('password_confirmation') is-invalid @enderror"
                                            type="password"
                                            name="password_confirmation"
                                            required
                                            autocomplete="new-password"
                                            placeholder="Şifreni tekrar gir"
                                        >
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary auth-btn">
                                        Hesap Oluştur
                                    </button>
                                </form>
                            </div>

                            <div class="col-12 col-lg-5">
                                <div class="h-100 rounded-4 border bg-light p-4">
                                    <div class="small fw-bold text-primary text-uppercase mb-2">Zaten Hesabın Var mı?</div>
                                    <h3 class="h4 fw-bold mb-3">Giriş yaparak devam et.</h3>
                                    <p class="text-muted mb-4">
                                        Hesabın zaten varsa yeni kayıt açmana gerek yok. Doğrudan giriş yapıp kaldığın yerden devam edebilirsin.
                                    </p>

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('login', array_filter(['redirect' => request('redirect')])) }}" class="btn btn-outline-dark auth-outline">
                                            Giriş Sayfasına Git
                                        </a>
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
