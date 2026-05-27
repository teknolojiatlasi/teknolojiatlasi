<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (config('services.turnstile.enabled') && config('services.turnstile.site_key'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
    @include('partials.seo')
    @include('partials.google-analytics')
    @include('partials.google-adsense')
    <link rel="stylesheet" href="{{ asset('vendor/front/company/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <style>
        body { background: #f5f7fb; }
        .hero { background: linear-gradient(135deg, #0d6efd, #6610f2); border-radius: 18px; color: #fff; }
        .participants-button {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: .55rem 1.1rem;
            border-radius: 1rem;
            background: linear-gradient(90deg, #a855f7 0%, #7c3aed 42%, #3b82f6 74%, #22d3ee 100%);
            color: #fff;
            box-shadow: 0 16px 34px rgba(124, 58, 237, .24), 0 10px 24px rgba(34, 211, 238, .16);
            text-align: center;
        }
        .participants-button small {
            display: block;
            margin-bottom: .2rem;
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .04em;
            color: rgba(255,255,255,.88);
        }
        .participants-button strong {
            display: block;
            font-size: clamp(1.35rem, 4vw, 1.9rem);
            line-height: 1;
            font-weight: 300;
            letter-spacing: -.02em;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="hero p-4 mb-3">
                    <h2 class="fw-bold mb-1">{{ $survey->title }}</h2>
                    <p class="mb-0">{{ $survey->description }}</p>
                </div>
                <div class="participants-button mb-3">
                    <small>Katılımcı Sayısı</small>
                    <strong>{{ $survey->responses_count }}</strong>
                </div>
                @include('partials.adsense.ad-unit', [
                    'slot' => 'survey_inline',
                    'style' => 'max-width: 100%;',
                    'label' => null,
                ])
                <x-survey::form :survey="$survey" :action="route('survey.public.submit', $survey)" submit-text="Yanıtı Gönder" />
                @include('partials.adsense.ad-unit', [
                    'slot' => 'survey_bottom',
                    'style' => 'max-width: 100%;',
                    'label' => null,
                ])
                @include('survey::partials.share-buttons', ['survey' => $survey])
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/front/company/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
