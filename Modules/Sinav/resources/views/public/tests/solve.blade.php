@extends('layouts.app2')
@section('content')

<main class="py-4">
    <div class="container">
        @include('partials.adsense.ad-unit', [
            'slot' => 'sinav_top',
            'class' => 'mx-auto',
            'style' => 'max-width: 1100px;',
            'insStyle' => 'display:block; text-align:center;',
            'layout' => 'in-article',
            'format' => 'fluid',
            'label' => null,
        ])

    <div class="row">
        <div class="col-12">
            @include('sinav::public.tests._wizard', ['test' => $test])
        </div>
    </div>

        @include('partials.adsense.ad-unit', [
            'slot' => 'sinav_bottom',
            'class' => 'mx-auto',
            'style' => 'max-width: 1100px;',
            'insStyle' => 'display:block; text-align:center;',
            'layout' => 'in-article',
            'format' => 'fluid',
            'label' => null,
        ])
    </div>
</main>
@endsection

