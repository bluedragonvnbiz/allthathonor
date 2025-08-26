<?php
/**
 * LiveChat Widget Initialization
 * Include this in footer to enable LiveChat widget
 */

// Only load on frontend
if (is_admin()) {
    return;
}

// Enqueue CSS
wp_enqueue_style(
    'livechat-widget-css',
    THEME_URL . '/assets/css/livechat-widget.css',
    [],
    '1.0.0'
);

// Enqueue JavaScript
wp_enqueue_script(
    'livechat-widget-js',
    THEME_URL . '/assets/js/livechat-widget.js',
    [],
    '1.0.0',
    true
);

// Include widget HTML
include get_template_directory() . '/app/Views/partials/livechat/widget.php';
?>