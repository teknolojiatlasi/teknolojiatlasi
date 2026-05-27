@php
    $adsenseClientId = config('services.google_adsense.client_id');
@endphp

@if ($adsenseClientId)
    @once
        <script async
            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseClientId }}"
            crossorigin="anonymous">
        </script>
    @endonce
@endif
