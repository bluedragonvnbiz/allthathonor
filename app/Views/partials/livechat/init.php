<?php
/**
 * LiveChat Widget Initialization
 */

// Only load on frontend
if (is_admin()) {
    return;
}

// Enqueue LiveChat JavaScript and initialize AJAX handler
wp_enqueue_script(
    'livechat-widget', 
    THEME_URL . '/assets/js/livechat-widget.js', 
    [], 
    '1.0.0', 
    true
);

// Include widget HTML
include get_template_directory() . '/app/Views/partials/livechat/widget.php';
?>