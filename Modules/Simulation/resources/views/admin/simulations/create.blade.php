@extends('app::layouts.admin')

@section('title', 'Simulasyon Olustur')

@section('content')
    <form method="POST" action="{{ route('simulation.admin.simulations.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-0">Yeni Simulasyon</h2>
                <small class="text-muted">Temel admin CRUD ekrani</small>
            </div>
            <div>
                <a href="{{ route('simulation.admin.simulations.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Listeye Don
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Kaydet
                </button>
            </div>
        </div>

        @include('simulation::admin.simulations._form')
    </form>
@endsection
