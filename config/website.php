<?php

return [
    'default_preset' => 'emerald',

    /*
    | Three curated storefront color templates.
    | Legacy keys (forest/slate/navy) map via legacy_map.
    */
    'presets' => [
        'emerald' => [
            'label' => 'Emerald',
            'description' => 'Hijau segar — default toko cup',
            'swatch' => '#059669',
            'css' => [
                '--brand-50' => '#ecfdf5',
                '--brand-100' => '#d1fae5',
                '--brand-200' => '#a7f3d0',
                '--brand-500' => '#10b981',
                '--brand-600' => '#059669',
                '--brand-700' => '#047857',
                '--brand-800' => '#065f46',
                '--brand-900' => '#064e3b',
                '--brand-fg' => '#ffffff',
                '--page-bg' => '#fafaf9',
            ],
        ],
        'ocean' => [
            'label' => 'Ocean',
            'description' => 'Biru laut + aksen cyan',
            'swatch' => '#0891b2',
            'css' => [
                '--brand-50' => '#ecfeff',
                '--brand-100' => '#cffafe',
                '--brand-200' => '#a5f3fc',
                '--brand-500' => '#06b6d4',
                '--brand-600' => '#0891b2',
                '--brand-700' => '#0e7490',
                '--brand-800' => '#155e75',
                '--brand-900' => '#164e63',
                '--brand-fg' => '#ffffff',
                '--page-bg' => '#f8fafc',
            ],
        ],
        'rose' => [
            'label' => 'Rose',
            'description' => 'Merah rose gelap + stone',
            'swatch' => '#e11d48',
            'css' => [
                '--brand-50' => '#fff1f2',
                '--brand-100' => '#ffe4e6',
                '--brand-200' => '#fecdd3',
                '--brand-500' => '#f43f5e',
                '--brand-600' => '#e11d48',
                '--brand-700' => '#be123c',
                '--brand-800' => '#9f1239',
                '--brand-900' => '#881337',
                '--brand-fg' => '#ffffff',
                '--page-bg' => '#fafaf9',
            ],
        ],
    ],

    'legacy_map' => [
        'forest' => 'emerald',
        'slate' => 'ocean',
        'navy' => 'rose',
    ],
];
