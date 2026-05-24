@extends('app::layouts.admin')

@section('title', $page->title)

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>{{ $page->title }}</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <p><strong>Slug:</strong> {{ $page->slug }}</p>
        <p><strong>Roller:</strong> {{ $page->roles->pluck('name')->implode(', ') }}</p>
        <hr>
        <div>{!! nl2br(e($page->content)) !!}</div>
    </div>
</div>
@endsection
