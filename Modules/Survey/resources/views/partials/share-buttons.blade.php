@props(['survey'])

@php
    $shareUrl = route('survey.public.show', $survey);
    $shareTitle = $survey->title;
    $encodedUrl = urlencode($shareUrl);
    $encodedText = urlencode($shareTitle.' - Ankete katıl');
@endphp

<div class="card shadow-sm mt-3">
    <div class="card-body">
        <div class="fw-semibold mb-3">Anketi paylaş</div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-success" href="https://wa.me/?text={{ $encodedText }}%20{{ $encodedUrl }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>
            <a class="btn btn-primary" href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank" rel="noopener noreferrer">Facebook</a>
            <a class="btn btn-info text-white" href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" target="_blank" rel="noopener noreferrer">LinkedIn</a>
            <a class="btn btn-dark" href="https://t.me/share/url?url={{ $encodedUrl }}&text={{ $encodedText }}" target="_blank" rel="noopener noreferrer">Telegram</a>
            <a class="btn btn-outline-secondary" style="background-color: lawngreen" href="{{ url('/sosial') }}">Mülakat Hazırlığı</a>
            <a class="btn btn-outline-primary" style="background-color: rgb(230, 132, 144)"  href="{{ url('/') }}">Anasayfa</a>
        </div>
        <div class="mt-3">
            <a class="btn btn-warning btn-lg w-100 fw-semibold" href="{{ url('/anketler') }}">Tüm Anketlere Git</a>
        </div>
    </div>
</div>
