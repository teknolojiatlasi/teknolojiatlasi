@extends('layouts.app2')

@section('title', 'CV Oluştur')

@section('content')
<main class="py-4 py-lg-5">
    <div class="container">
        @include('cv::admin._form')
    </div>
</main>
@endsection
