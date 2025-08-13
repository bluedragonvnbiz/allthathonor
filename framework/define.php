<?php
define( 'BJ_NONCE_KEY', "BJWPN0483rwrHSD" );

define( 'THEME_URL', trailingslashit( esc_url( get_template_directory_uri() ) ) );
define( 'SITE_URL', trailingslashit( esc_url( get_site_url() ) ) );

define( 'CUSTOMER', "subscriber" );
define( 'SHOPMANAGER', "shopmanager" );
define( 'GOOGLE_KEY', "AIzaSyDCSlgEBP4NVa4smqnqRp5CJvv5ZfljY-M" );

define("time_morning_array", array(137,138,139,140,6,7,8,9,10,11,12,13));
define("time_afternoon_array", array(14,15,16,17,18,19,20,21,22,23,24,26));
define("time_evening_array", array(27,28,30,31,32,33,34,142,143,144,145,146));

define("POST_TYPE_ARR", array( 1 => "nail", 2 => "women", 3 => "hair",4 => "makeup" ));
define("POST_TYPE_LABEL", array( "nail" => "Nail","women" => "Women's Hair","hair" => "Men's Hair","makeup" => "Makeup" ));
define("CATEGORY_ARR", array( 1 => "Nail", 2 => "Women's Hair", 3 => "Men's Hair",4 => "Makeup" ));
define("CATEGORY_TERM_SIZE_ARR", array(  
    "nail" => array("long" => 201,"medium" => 202,"short" => 203),
    "hair" => array("long" => 74,"medium" => 82,"short" => 132),
    "women" => array("long" => 75,"medium" => 83,"short" => 131)
));

