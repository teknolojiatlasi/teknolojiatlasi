@extends('app::layouts.admin')

@section('title', 'İletişim - Mesaj')

@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>Mesaj</h2>
        </div>
        <div class="x_content">
            @include('contact::admin.messages._show', ['message' => $message])
        </div>
    </div>
@endsection

