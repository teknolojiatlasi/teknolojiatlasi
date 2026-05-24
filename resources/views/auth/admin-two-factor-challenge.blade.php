@extends('layouts.app2')

@section('title', 'Yönetim Doğrulaması')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="mb-4">
                        <div class="small text-uppercase fw-bold text-primary mb-2">Ek güvenlik</div>
                        <h1 class="h3 fw-bold mb-2">Yönetim girişini doğrula</h1>
                        <p class="text-muted mb-0">E-posta adresinize gönderilen 6 haneli kodu girin.</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success rounded-4">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.mfa.verify') }}" class="d-grid gap-3">
                        @csrf
                        <div>
                            <label for="code" class="form-label fw-semibold">Doğrulama kodu</label>
                            <input
                                id="code"
                                name="code"
                                type="text"
                                inputmode="numeric"
                                maxlength="6"
                                class="form-control @error('code') is-invalid @enderror"
                                value="{{ old('code') }}"
                                required
                                autofocus
                            >
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Doğrula</button>
                    </form>

                    <form method="POST" action="{{ route('admin.mfa.resend') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">Kodu yeniden gönder</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-dark">Çıkış yap</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
