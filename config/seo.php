<?php

return [
    'site_name' => env('SEO_SITE_NAME', config('app.name', 'Teknoloji Atlası')),
    'default_title' => env('SEO_DEFAULT_TITLE', config('app.name', 'Teknoloji Atlası')),
    'default_description' => env('SEO_DEFAULT_DESCRIPTION', 'Teknoloji yazilari, blog, sinav, anket ve sosyal icerikleri hizli acilan sayfalarda sunan bilgi platformu.'),
    'default_image' => env('SEO_DEFAULT_IMAGE', 'vendor/front/company/assets/img/logo.png'),
    'twitter_site' => env('SEO_TWITTER_SITE'),
];
