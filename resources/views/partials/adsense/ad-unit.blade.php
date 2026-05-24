@props([
    'slot' => null,
    'format' => 'auto',
    'layout' => null,
    'responsive' => true,
    'variant' => 'default',
    'class' => '',
    'style' => '',
    'insStyle' => 'display:block',
    'minHeight' => null,
    'label' => '',
])

@php
    $adsenseClientId = config('services.google_adsense.client_id');
    $resolvedSlot = filled($slot) ? config("services.google_adsense.slots.$slot", $slot) : null;
    $resolvedMinHeight = $minHeight ?: match ($variant) {
        'hero', 'banner' => '280px',
        'sidebar' => '320px',
        'feed', 'inline' => '180px',
        default => '200px',
    };
    $wrapperClasses = trim('adsense-block adsense-block-' . $variant . ' ' . $class);
    $wrapperStyle = trim(collect([
        "min-height: {$resolvedMinHeight};",
        "contain-intrinsic-size: {$resolvedMinHeight};",
        $style ?: null,
    ])->filter()->implode(' '));
@endphp

@if ($adsenseClientId && filled($resolvedSlot))
    @once
        <style>
            .adsense-block {
                margin: 1.5rem 0;
                padding: 0;
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
                overflow: hidden;
                border: 0;
                border-radius: 1rem;
                background: transparent;
                position: relative;
            }

            .adsense-block-inline {
                margin: 1.75rem 0;
            }

            .adsense-block-hero,
            .adsense-block-banner {
                margin: 0 0 2rem;
            }

            .adsense-block-sidebar {
                position: sticky;
                top: 1.25rem;
            }

            .adsense-block-feed {
                margin: 0.5rem 0 1.5rem;
            }

            .adsense-block .adsbygoogle {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                overflow: hidden !important;
                min-height: inherit;
                min-width: 100%;
            }

            .adsense-block::before {
                content: "";
                position: absolute;
                inset: 0;
                border-radius: 0.85rem;
                background: transparent;
            }

            .adsense-block[data-ads-ready="true"]::before {
                display: none;
            }

            .adsense-block-label {
                display: inline-block;
                margin-bottom: 0.75rem;
                color: #64748b;
                font-size: 0.75rem;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            @media (max-width: 575.98px) {
                .adsense-block {
                    margin: 1rem 0;
                    padding: 0.75rem;
                    border-radius: 0.85rem;
                }

                .adsense-block-sidebar {
                    position: static;
                }
            }
        </style>
        <script>
            (function () {
                if (window.__adsenseLazyInit) {
                    return;
                }

                window.__adsenseLazyInit = true;

                function loadAdsenseScript() {
                    if (window.__adsenseScriptPromise) {
                        return window.__adsenseScriptPromise;
                    }

                    window.__adsenseScriptPromise = new Promise(function (resolve, reject) {
                        var script = document.createElement('script');
                        script.async = true;
                        script.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseClientId }}';
                        script.crossOrigin = 'anonymous';
                        script.onload = resolve;
                        script.onerror = reject;
                        document.head.appendChild(script);
                    });

                    return window.__adsenseScriptPromise;
                }

                function pushAd(slot) {
                    if (!slot || slot.dataset.adsLoaded === 'true') {
                        return;
                    }

                    loadAdsenseScript()
                        .then(function () {
                            try {
                                (window.adsbygoogle = window.adsbygoogle || []).push({});
                                slot.dataset.adsLoaded = 'true';
                                var wrapper = slot.closest('.adsense-block');
                                if (wrapper) {
                                    wrapper.setAttribute('data-ads-ready', 'true');
                                }
                            } catch (error) {
                                // Ignore duplicate pushes or ad blockers.
                            }
                        })
                        .catch(function () {
                            // Ignore blocked ad requests.
                        });
                }

                function observeAds() {
                    var slots = Array.prototype.slice.call(document.querySelectorAll('.adsbygoogle[data-auto-ads-slot="1"]'));
                    if (!slots.length) {
                        return;
                    }

                    if (!('IntersectionObserver' in window)) {
                        slots.forEach(pushAd);
                        return;
                    }

                    var observer = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (!entry.isIntersecting) {
                                return;
                            }

                            pushAd(entry.target);
                            observer.unobserve(entry.target);
                        });
                    }, {
                        rootMargin: '150px 0px',
                    });

                    slots.forEach(function (slot) {
                        observer.observe(slot);
                    });
                }

                function scheduleAds() {
                    if ('requestIdleCallback' in window) {
                        requestIdleCallback(observeAds, { timeout: 5000 });
                        return;
                    }

                    setTimeout(observeAds, 3000);
                }

                if (document.readyState === 'complete') {
                    scheduleAds();
                } else {
                    window.addEventListener('load', scheduleAds, { once: true });
                }
            })();
        </script>
    @endonce

    <div class="{{ $wrapperClasses }}" data-ads-ready="false" @if($wrapperStyle) style="{{ $wrapperStyle }}" @endif>
        @if (filled($label))
            <div class="adsense-block-label">{{ $label }}</div>
        @endif
        <ins class="adsbygoogle"
             style="{{ $insStyle }}"
             data-auto-ads-slot="1"
             data-ad-client="{{ $adsenseClientId }}"
             data-ad-slot="{{ $resolvedSlot }}"
             data-ad-format="{{ $format }}"
             @if (filled($layout)) data-ad-layout="{{ $layout }}" @endif
             data-full-width-responsive="{{ $responsive ? 'true' : 'false' }}"></ins>
    </div>
@endif
