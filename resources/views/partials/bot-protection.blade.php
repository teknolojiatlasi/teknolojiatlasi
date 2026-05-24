<input type="text" name="_trap" value="" tabindex="-1" autocomplete="off" class="d-none" aria-hidden="true">
<input type="hidden" name="_started_at" value="{{ now()->timestamp }}">

@if (config('services.turnstile.enabled') && config('services.turnstile.site_key'))
    <div class="mt-3">
        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
    </div>
@endif
