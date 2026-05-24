@extends('layouts.admin')

@section('title', 'Kullanici Rolleri')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>{{ $user->name }} - Rol Duzenle</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <p><strong>E-posta:</strong> {{ $user->email }}</p>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            @php
                $isAdminUser = ! $user->hasRole('superadmin') && $user->hasRole('admin');
                $selectedRoles = collect(old('roles', $user->roles->pluck('id')->all()))
                    ->map(fn ($roleId) => (int) $roleId)
                    ->all();
            @endphp

            <div class="mb-3">
                <label class="form-label">Roller</label>

                @forelse($roles as $role)
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="roles[]"
                            value="{{ $role->id }}"
                            id="role_{{ $role->id }}"
                            @checked(in_array($role->id, $selectedRoles, true))
                        >
                        <label class="form-check-label" for="role_{{ $role->id }}">
                            {{ $role->name }}
                        </label>
                    </div>
                @empty
                    <p>Once rollerin olusturulmus olmasi gerekir.</p>
                @endforelse
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="two_factor_required"
                        value="1"
                        id="two_factor_required"
                        @checked($isAdminUser || old('two_factor_required', $user->two_factor_required))
                        @disabled($isAdminUser)
                    >
                    <label class="form-check-label" for="two_factor_required">
                        Bu kullanici icin iki adimli dogrulama zorunlu olsun
                    </label>
                </div>
                <small class="text-muted">
                    @if($isAdminUser)
                        Bu kullanici sadece admin yetkisinde oldugu icin iki adimli dogrulama kapatilamaz.
                    @else
                        Superadmin yetkisi olan kullanicilar dahil bu secimi siz belirlersiniz.
                    @endif
                </small>
            </div>

            <button type="submit" class="btn btn-success">Yetkileri Kaydet</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Geri Don</a>
        </form>
    </div>
</div>
@endsection
