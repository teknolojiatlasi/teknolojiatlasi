<?php

return [
    'csp' => [
        'default_src' => ["'self'"],
        'base_uri' => ["'self'"],
        'object_src' => ["'none'"],
        'form_action' => ["'self'"],
        'frame_ancestors' => ["'self'"],
        'script_src' => [
            "'self'",
            "'unsafe-inline'",
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
            'https://www.googletagmanager.com',
            'https://www.google-analytics.com',
            'https://pagead2.googlesyndication.com',
            'https://googleads.g.doubleclick.net',
            'https://partner.googleadservices.com',
            'https://challenges.cloudflare.com',
        ],
        'style_src' => [
            "'self'",
            "'unsafe-inline'",
            'https://cdn.jsdelivr.net',
            'https://fonts.googleapis.com',
        ],
        'img_src' => [
            "'self'",
            'data:',
            'blob:',
            'https:',
        ],
        'font_src' => [
            "'self'",
            'data:',
            'https://fonts.gstatic.com',
        ],
        'connect_src' => [
            "'self'",
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
            'https://www.google-analytics.com',
            'https://region1.google-analytics.com',
            'https://pagead2.googlesyndication.com',
            'https://googleads.g.doubleclick.net',
            'https://challenges.cloudflare.com',
            'https://nominatim.openstreetmap.org',
            'https://*.tile.openstreetmap.org',
        ],
        'frame_src' => [
            "'self'",
            'https://googleads.g.doubleclick.net',
            'https://tpc.googlesyndication.com',
            'https://challenges.cloudflare.com',
        ],
        'worker_src' => [
            "'self'",
            'blob:',
        ],
        'manifest_src' => [
            "'self'",
        ],
        'media_src' => [
            "'self'",
            'blob:',
        ],
        'upgrade_insecure_requests' => (bool) env('CSP_UPGRADE_INSECURE_REQUESTS', true),
    ],

    'uploads' => [
        'max_bytes' => (int) env('UPLOAD_MAX_BYTES', 4 * 1024 * 1024),
        'max_pixels' => (int) env('UPLOAD_MAX_PIXELS', 40_000_000),
        'min_width' => (int) env('UPLOAD_MIN_WIDTH', 1),
        'min_height' => (int) env('UPLOAD_MIN_HEIGHT', 1),
        'antivirus_command' => env('UPLOAD_ANTIVIRUS_COMMAND'),
    ],

    'alerts' => [
        'burst_window_minutes' => (int) env('SECURITY_ALERT_WINDOW_MINUTES', 10),
        'burst_threshold' => (int) env('SECURITY_ALERT_THRESHOLD', 10),
    ],
];
