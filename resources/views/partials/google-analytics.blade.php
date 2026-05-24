@php($measurementId = config('services.google_analytics.measurement_id'))

@if ($measurementId)
    <script>
        (function () {
            if (window.__gaLoaderInit) {
                return;
            }

            window.__gaLoaderInit = true;

            function bootAnalytics() {
                if (window.__gaLoaded) {
                    return;
                }

                window.__gaLoaded = true;
                window.dataLayer = window.dataLayer || [];
                window.gtag = function () {
                    window.dataLayer.push(arguments);
                };

                var script = document.createElement('script');
                script.async = true;
                script.src = 'https://www.googletagmanager.com/gtag/js?id={{ $measurementId }}';
                document.head.appendChild(script);

                window.gtag('js', new Date());
                window.gtag('config', '{{ $measurementId }}');
            }

            function scheduleAnalytics() {
                if ('requestIdleCallback' in window) {
                    requestIdleCallback(bootAnalytics, { timeout: 4000 });
                    return;
                }

                setTimeout(bootAnalytics, 2500);
            }

            if (document.readyState === 'complete') {
                scheduleAnalytics();
                return;
            }

            window.addEventListener('load', scheduleAnalytics, { once: true });
        })();
    </script>
@endif
