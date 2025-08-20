<?php
/**
 * Security Class
 * Handle security features
 */
class HonorsSecurity {
    
    public function __construct() {
        $this->disable_feeds();
        $this->restrict_rest_api();
    }
    
    /**
     * Disable RSS feeds
     */
    private function disable_feeds() {
        add_action('do_feed', [$this, 'disable_feed'], 1);
        add_action('do_feed_rdf', [$this, 'disable_feed'], 1);
        add_action('do_feed_rss', [$this, 'disable_feed'], 1);
        add_action('do_feed_rss2', [$this, 'disable_feed'], 1);
        add_action('do_feed_atom', [$this, 'disable_feed'], 1);
        add_action('do_feed_rss2_comments', [$this, 'disable_feed'], 1);
        add_action('do_feed_atom_comments', [$this, 'disable_feed'], 1);
    }
    
    /**
     * Disable feed function
     */
    public function disable_feed() {
        die( "Hello from pvt" );
        return;
    }
    
    /**
     * Restrict REST API access
     */
    private function restrict_rest_api() {
        add_filter( 'rest_authentication_errors', [$this, 'restrict_rest_access'] );
    }
    
    /**
     * Restrict REST API to logged in users only
     */
    public function restrict_rest_access( $result ) {
        if ( ! empty( $result ) ) {
            return $result;
        }
        if ( ! is_user_logged_in() ) {
            return new WP_Error( 'hello', 'from pvt', array( 'status' => '' ) );
        }
        return $result;
    }
} 