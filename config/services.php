<?php
/**
 * Services Configuration
 * Configure framework services
 */
return [
    'services' => [
        'enqueue' => [
            'class' => 'HonorsEnqueue',
            'priority' => 10
        ],
        'ajax' => [
            'class' => 'HonorsAjaxLoader',
            'priority' => 20,
            'singleton' => true
        ],
        'cache' => [
            'class' => 'HonorsAdminCache',
            'priority' => 30,
            'admin_only' => true
        ],
        'security' => [
            'class' => 'HonorsSecurity',
            'priority' => 5
        ],
        'url_rewrite' => [
            'class' => 'HonorsUrlRewrite',
            'priority' => 15
        ],
        'css_manager' => [
            'class' => 'HonorsCSSManager',
            'priority' => 25,
            'singleton' => true
        ],
        'js_manager' => [
            'class' => 'HonorsJSManager',
            'priority' => 26,
            'singleton' => true
        ]
    ],
    
    'autoload' => [
        'framework' => [
            'Core' => '/framework/Core',
            'Services' => '/framework/Services',
            'Controllers' => '/framework/Controllers',
            'Views' => '/framework/Views',
            'Middleware' => '/framework/Middleware',
            'Helpers' => '/framework/Helpers'
        ],
        'app' => [
            'Controllers' => '/app/Controllers',
            'Views' => '/app/Views',
            'Models' => '/app/Models',
            'Ajax' => '/app/Ajax'
        ]
    ]
];
