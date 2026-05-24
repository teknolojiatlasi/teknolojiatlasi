@extends('layouts.app2')

@section('title', 'CV Düzenle')

@section('content')
<main class="py-4 py-lg-5">
    <div class="container">
        @include('cv::admin._form', ['cv' => $cv])
    </div>
</main>
@endsection
