<?php 
add_action( 'init', 'my_custom_dashboard_access_handler');
function my_custom_dashboard_access_handler() {

   // Check if the current page is an admin page
   // && and ensure that this is not an ajax call
   if ( is_admin() && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ){
      
      //Get all capabilities of the current user
      $user = get_userdata( get_current_user_id() );
      $caps = ( is_object( $user) ) ? array_keys($user->allcaps) : array();

      //All capabilities/roles listed here are not able to see the dashboard
      $block_access_to = array(SHOPMANAGER,CUSTOMER);
      
      if(array_intersect($block_access_to, $caps)) {
        wp_redirect( SITE_URL."404" );         
         exit;
      }
   }
}


add_action('after_setup_theme', 'disable_admin_bar'); 
function disable_admin_bar() { 
  if (current_user_can(SHOPMANAGER)) { 
    show_admin_bar(false); 
  };
  if (current_user_can(CUSTOMER)) { 
    show_admin_bar(false); 
  };
}


// add_action( 'init', 'setting_my_first_lang' );
// function setting_my_first_lang() {
//   $cookie_name = "bj_lang";
//   if(!isset($_COOKIE[$cookie_name])) {
//     setcookie($cookie_name, "vi",time()+604800, "/","", 0);

//   }

// }

// add_filter('init','wp_noshor_redefine_locale');
// function wp_noshor_redefine_locale($locale) {
//     if(!is_admin()){
//       if(is_lang("en_US")){
//         switch_to_locale('en_US');
//       }else{
//         switch_to_locale('vi');
//       }
//     }   
// }
