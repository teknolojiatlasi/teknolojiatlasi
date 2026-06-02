<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SOCIAL LOGIN PROVIDERS
    |--------------------------------------------------------------------------
    */

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    'github' => [
        'client_id'     => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect'      => env('GITHUB_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        // Keep env naming consistent with provided project .env
        'redirect'      => env('FACEBOOK_REDIRECT_URL'),
    ],

    'turnstile' => [
        'enabled' => (bool) env('TURNSTILE_ENABLED', false),
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

    'google_analytics' => [
        'measurement_id' => env('GOOGLE_ANALYTICS_ID'),
    ],

    'google_adsense' => [
        'client_id' => env('GOOGLE_ADSENSE_CLIENT_ID', 'ca-pub-4508817626871635'),
        'slots' => [
            'home_top' => env('GOOGLE_ADSENSE_SLOT_HOME_TOP', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE')),
            'home_bottom' => env('GOOGLE_ADSENSE_SLOT_HOME_BOTTOM', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE')),
            'home_inline' => env('GOOGLE_ADSENSE_SLOT_HOME_INLINE'),
            'blog_top' => env('GOOGLE_ADSENSE_SLOT_BLOG_TOP', env('GOOGLE_ADSENSE_SLOT_HOME_TOP', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE'))),
            'blog_inline' => env('GOOGLE_ADSENSE_SLOT_BLOG_INLINE', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE')),
            'blog_bottom' => env('GOOGLE_ADSENSE_SLOT_BLOG_BOTTOM', env('GOOGLE_ADSENSE_SLOT_HOME_BOTTOM', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE'))),
            'blog_sidebar' => env('GOOGLE_ADSENSE_SLOT_BLOG_SIDEBAR', env('GOOGLE_ADSENSE_SLOT_BLOG_INLINE', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE'))),
            'blog_feed' => env('GOOGLE_ADSENSE_SLOT_BLOG_FEED', env('GOOGLE_ADSENSE_SLOT_BLOG_INLINE', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE'))),
            'sosial_top' => env('GOOGLE_ADSENSE_SLOT_SOSIAL_TOP', env('GOOGLE_ADSENSE_SLOT_HOME_TOP', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE'))),
            'sosial_bottom' => env('GOOGLE_ADSENSE_SLOT_SOSIAL_BOTTOM', env('GOOGLE_ADSENSE_SLOT_HOME_BOTTOM', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE'))),
            'sinav_top' => env('GOOGLE_ADSENSE_SLOT_SINAV_TOP', env('GOOGLE_ADSENSE_SLOT_HOME_TOP', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE'))),
            'sinav_bottom' => env('GOOGLE_ADSENSE_SLOT_SINAV_BOTTOM', env('GOOGLE_ADSENSE_SLOT_HOME_BOTTOM', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE'))),
            'simulation_top' => env('GOOGLE_ADSENSE_SLOT_SIMULATION_TOP', env('GOOGLE_ADSENSE_SLOT_BLOG_TOP', env('GOOGLE_ADSENSE_SLOT_HOME_TOP', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE')))),
            'simulation_inline' => env('GOOGLE_ADSENSE_SLOT_SIMULATION_INLINE', env('GOOGLE_ADSENSE_SLOT_BLOG_INLINE', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE'))),
            'simulation_bottom' => env('GOOGLE_ADSENSE_SLOT_SIMULATION_BOTTOM', env('GOOGLE_ADSENSE_SLOT_BLOG_BOTTOM', env('GOOGLE_ADSENSE_SLOT_HOME_BOTTOM', env('GOOGLE_ADSENSE_SLOT_HOME_INLINE')))),
            'survey_inline' => env('GOOGLE_ADSENSE_SLOT_SURVEY_INLINE'),
            'survey_bottom' => env('GOOGLE_ADSENSE_SLOT_SURVEY_BOTTOM'),
        ],
    ],

    'webpush' => [
        'enabled' => (bool) env('WEBPUSH_ENABLED', true),
        'vapid' => [
            'subject' => env('WEBPUSH_VAPID_SUBJECT'),
            'public_key' => env('WEBPUSH_VAPID_PUBLIC_KEY'),
            'private_key' => env('WEBPUSH_VAPID_PRIVATE_KEY'),
        ],
    ],

];
