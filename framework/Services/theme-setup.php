<?php
/**
 * Theme Setup Class
 * Handle theme initialization and setup
 */
class HonorsThemeSetup {
    
    public function __construct() {
        add_action( 'after_setup_theme', [$this, 'setup'] );
        add_filter('use_block_editor_for_post', '__return_false');
    }
    
    /**
     * Theme setup
     */
    public function setup() {
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
    }
} 