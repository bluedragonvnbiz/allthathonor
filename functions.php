<?php
define( 'BJ_NONCE_KEY', "BJWPN0483rwrHSD" );
define( 'THEME_URL', get_template_directory_uri() );
add_action( 'after_setup_theme', 'stylevook_setup' );
function stylevook_setup(){
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    //define( 'current_user_id', get_current_user_id());
}

include_once "framework/lib.php";
include_once "inc/login/login.php";

add_action( 'wp_enqueue_scripts', 'pv_styles' );
function pv_styles() {
    wp_enqueue_script('jquery');
    wp_register_style( 'bootstrap-style',  THEME_URL.'/assets/lib/bootstrap.min.css', 'all' );
    wp_enqueue_style( 'bootstrap-style' );

    if(is_page("management")){
        wp_register_style( 'management-style', THEME_URL. '/assets/css/management.css', 'all',"2.15" );
        wp_enqueue_style( 'management-style' );
        wp_register_script('management-script', THEME_URL. '/assets/js/management.js', array(),false, true);
        wp_enqueue_script('management-script');
    }else{
        wp_register_style( 'swiper-style',  THEME_URL.'/assets/lib/swiper-bundle.min.css', 'all' );
        wp_enqueue_style( 'swiper-style' );  
        wp_register_style( 'cus-style', THEME_URL. '/assets/global-css.css', 'all',"2.15" );
        wp_enqueue_style( 'cus-style' );
        wp_register_script('swiper-script', THEME_URL. '/assets/lib/wiper-bundle.min.js', array(),false, true);
        wp_enqueue_script('swiper-script');
    }
    
    wp_register_style( 'wp-style', THEME_URL. '/style.css', 'all',"4" );
    wp_enqueue_style( 'wp-style' );
    wp_register_script('tanpv-js', THEME_URL . '/assets/global-script.js', array(), "1.7", true);

    wp_localize_script('tanpv-js', 'define', 
        array(
            'ajax_url' => admin_url('admin-ajax.php')
        )
    );
    wp_enqueue_script('tanpv-js');
    wp_dequeue_style( 'wp-block-library' );

}


add_action( 'admin_enqueue_scripts', 'my_admin_style');
function my_admin_style() {
  wp_enqueue_style( 'admin-style', get_stylesheet_directory_uri() . '/assets/admin/assets/style.css?ver=2' );
  wp_enqueue_script('admin-script', get_stylesheet_directory_uri()  . '/assets/admin/assets/style.js');
}

function wpb_disable_feed() {
die( "Hello from pvt" );return;
}
 
add_action('do_feed', 'wpb_disable_feed', 1);
add_action('do_feed_rdf', 'wpb_disable_feed', 1);
add_action('do_feed_rss', 'wpb_disable_feed', 1);
add_action('do_feed_rss2', 'wpb_disable_feed', 1);
add_action('do_feed_atom', 'wpb_disable_feed', 1);
add_action('do_feed_rss2_comments', 'wpb_disable_feed', 1);
add_action('do_feed_atom_comments', 'wpb_disable_feed', 1);

add_filter( 'rest_authentication_errors', function( $result ) {
    if ( ! empty( $result ) ) {
        return $result;
    }
    if ( ! is_user_logged_in() ) {
        return new WP_Error( 'hello', 'from pvt', array( 'status' => '' ) );
    }
    return $result;
});

function my_login_stylesheet() {
    wp_enqueue_style( 'custom-login', THEME_URL. 'inc/login/login.css','all', "1");
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );

add_filter('use_block_editor_for_post', '__return_false');
