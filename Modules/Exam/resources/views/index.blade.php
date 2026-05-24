@extends('exam::components.layouts.master')

@section('title', 'Sınav Modülü')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1>Sınav Modülü</h1>
                </div>
                <div class="card-body">
                    <p>Modül: {!! config('exam.name') !!}</p>
                    <div class="mt-3">
                        <a href="{{ route('exam.menus.index') }}" class="btn btn-primary">Menüleri Yönet</a>
                        <a href="{{ route('exam.exams.index') }}" class="btn btn-secondary">Sınavları Yönet</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
