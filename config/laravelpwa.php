<?php

return [
    'name' => 'LaravelPWA',
    'manifest' => [
        'name' => env('APP_NAME', 'Bilgi Yildizi'),
        'short_name' => 'Bilgi',
        'start_url' => '/',
        'background_color' => '#f8fafc',
        'theme_color' => '#0f172a',
        'display' => 'standalone',
        'orientation'=> 'portrait',
        'status_bar'=> 'default',
        'icons' => [
            '192x192' => [
                'path' => '/images/icons/by-star-192x192.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/images/icons/by-star-512x512.png',
                'purpose' => 'any'
            ],
            '512x512-maskable' => [
                'path' => '/images/icons/by-star-512x512.png',
                'sizes' => '512x512',
                'purpose' => 'maskable'
            ],
        ],
        'splash' => [
            '640x1136' => '/images/icons/splash-640x1136.png',
            '750x1334' => '/images/icons/splash-750x1334.png',
            '828x1792' => '/images/icons/splash-828x1792.png',
            '1125x2436' => '/images/icons/splash-1125x2436.png',
            '1242x2208' => '/images/icons/splash-1242x2208.png',
            '1242x2688' => '/images/icons/splash-1242x2688.png',
            '1536x2048' => '/images/icons/splash-1536x2048.png',
            '1668x2224' => '/images/icons/splash-1668x2224.png',
            '1668x2388' => '/images/icons/splash-1668x2388.png',
            '2048x2732' => '/images/icons/splash-2048x2732.png',
        ],
        'shortcuts' => [
            [
                'name' => 'Ana Sayfa',
                'description' => 'Bilgi Yildizi ana sayfasini ac',
                'url' => '/',
                'icons' => [
                    "src" => "/images/icons/by-star-192x192.png",
                    "purpose" => "any"
                ]
            ],
            [
                'name' => 'Giris',
                'description' => 'Hesabina giris yap',
                'url' => '/login',
                'icons' => [
                    "src" => "/images/icons/by-star-192x192.png",
                    "purpose" => "any"
                ]
            ]
        ],
        'custom' => [
            'description' => 'Bilgi Yildizi modullerini mobil cihazlarda uygulama gibi kullanin.',
            'lang' => 'tr',
            'dir' => 'ltr',
            'categories' => ['education', 'productivity', 'business'],
        ]
    ]
];
