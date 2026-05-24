@php
    $seoData = $seo ?? [];
    $siteName = $seoData['site_name'] ?? config('seo.site_name', config('app.name'));
    $sectionTitle = trim($__env->yieldContent('title'));
    $sectionDescription = trim($__env->yieldContent('meta_description'));
    $pageTitle = $sectionTitle !== '' ? $sectionTitle : ($seoData['title'] ?? $siteName);
    $description = $sectionDescription !== '' ? $sectionDescription : ($seoData['description'] ?? config('seo.default_description'));
    $canonical = trim($__env->yieldContent('canonical')) !== '' ? trim($__env->yieldContent('canonical')) : ($seoData['canonical'] ?? url()->current());
    $robots = $seoData['robots'] ?? 'index, follow';
    $image = $seoData['image'] ?? null;
    $type = $seoData['type'] ?? 'website';
    $publishedTime = $seoData['published_time'] ?? null;
    $modifiedTime = $seoData['modified_time'] ?? null;
    $twitterSite = config('seo.twitter_site');
@endphp
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:locale" content="tr_TR">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
@if ($image)
<meta property="og:image" content="{{ $image }}">
@endif
@if ($publishedTime)
<meta property="article:published_time" content="{{ $publishedTime }}">
@endif
@if ($modifiedTime)
<meta property="article:modified_time" content="{{ $modifiedTime }}">
@endif
<meta name="twitter:card" content="{{ $image ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $description }}">
@if ($twitterSite)
<meta name="twitter:site" content="{{ $twitterSite }}">
@endif
@if ($image)
<meta name="twitter:image" content="{{ $image }}">
@endif
@foreach (($seoData['schema'] ?? []) as $schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endforeach
