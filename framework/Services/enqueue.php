<?php
/**
 * Enqueue Manager Class
 * Handle all CSS and JS loading with CSS Manager integration
 */
class HonorsEnqueue {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'frontend_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
    }
    
    /**
     * Frontend scripts and styles
     */
    public function frontend_scripts() {
        // jQuery
        wp_enqueue_script('jquery');
        
        // Bootstrap CSS & JS
        wp_enqueue_style('bootstrap-style', THEME_URL . '/assets/lib/bootstrap.min.css', [], '5.3.0');
        wp_enqueue_script('bootstrap-script', THEME_URL . '/assets/lib/bootstrap.bundle.min.js', ['jquery'], '5.3.0', true);
        
        // Swiper CSS & JS
        wp_enqueue_style('swiper-style', THEME_URL . '/assets/lib/swiper-bundle.min.css', [], '10.0.0');
        wp_enqueue_script('swiper-script', THEME_URL . '/assets/lib/swiper-bundle.min.js', [], '10.0.0', true);
        
        // Global theme assets
        wp_enqueue_style('theme-style', THEME_URL . '/style.css', [], '4.0.0');
        wp_enqueue_script('global-script', THEME_URL . '/assets/js/global-script.js', ['jquery'], '1.0.1', true);
        
        // Localize script
        wp_localize_script('global-script', 'define', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'theme_url' => THEME_URL,
            'nonce' => wp_create_nonce('honors_nonce')
        ]);
        
        // Disable block library
        wp_dequeue_style('wp-block-library');
    }
    
    /**
     * Admin scripts and styles
     */
    public function admin_scripts() {
        wp_enqueue_style('admin-style', THEME_URL . '/assets/admin/assets/style.css', [], '2.0.0');
        wp_enqueue_script('admin-script', THEME_URL . '/assets/admin/assets/style.js', ['jquery'], '2.0.0', true);
    }
} 