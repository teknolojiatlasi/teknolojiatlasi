@extends('app::layouts.admin')

@section('title', 'Simulasyon Duzenle')

@section('content')
    <form method="POST" action="{{ route('simulation.admin.simulations.update', $simulation) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-0">Simulasyon Duzenle</h2>
                <small class="text-muted">Slug: {{ $simulation->slug }}</small>
            </div>
            <div>
                <a href="{{ route('simulation.admin.simulations.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Listeye Don
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Guncelle
                </button>
            </div>
        </div>

        @include('simulation::admin.simulations._form')
    </form>
@endsection
