<?php
/**
 * Application Configuration
 * Main application settings
 */
return [
    'app' => [
        'name' => 'All That Honors Club',
        'version' => '2.0.0',
        'debug' => WP_DEBUG,
        'timezone' => 'Asia/Seoul',
        'locale' => 'ko_KR'
    ],
    
    'theme' => [
        'supports' => [
            'title-tag',
            'post-thumbnails',
            'custom-logo',
            'html5' => [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption'
            ]
        ],
        'menus' => [
            'primary' => 'Primary Menu',
            'footer' => 'Footer Menu'
        ],
        'sidebars' => [
            'sidebar-1' => 'Main Sidebar',
            'footer-1' => 'Footer Widget Area 1',
            'footer-2' => 'Footer Widget Area 2'
        ]
    ],
    
    'security' => [
        'nonce_name' => 'honors_nonce',
        'nonce_action' => 'honors_action',
        'allowed_html' => [
            'a' => [
                'href' => [],
                'title' => [],
                'target' => []
            ],
            'br' => [],
            'em' => [],
            'strong' => [],
            'p' => [],
            'div' => [
                'class' => []
            ],
            'span' => [
                'class' => []
            ]
        ]
    ],
    
    'cache' => [
        'enabled' => true,
        'duration' => 3600, // 1 hour
        'prefix' => 'honors_'
    ]
];
