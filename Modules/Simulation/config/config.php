<?php

return [
    'name' => 'Simulation',
    'editor' => [
        'driver' => env('SIMULATION_EDITOR_DRIVER', 'monaco'),
        'preview_debounce_ms' => (int) env('SIMULATION_PREVIEW_DEBOUNCE_MS', 400),
        'iframe_sandbox' => env('SIMULATION_IFRAME_SANDBOX', 'allow-scripts allow-same-origin'),
    ],
    'content_types' => [
        'html',
        'video',
        'image',
    ],
    'video_sources' => [
        'upload',
        'youtube',
        'vimeo',
    ],
    'statuses' => [
        'draft',
        'scheduled',
        'published',
        'archived',
    ],
];
