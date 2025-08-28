<?php
/**
 * All That Honor Theme Functions
 * Main functions file for the theme
 */

// Define theme constants
define('THEME_URL', get_template_directory_uri());
define('THEME_PATH', get_template_directory());

// Load autoloader
require_once get_template_directory() . '/framework/Services/autoloader.php';

// Initialize autoloader
$autoloader = HonorsAutoloader::getInstance();
$ajax_loader = HonorsAjaxLoader::getInstance();

// Load helpers (not autoloaded)
require_once get_template_directory() . '/framework/Helpers/helpers.php';

// Load database
require_once get_template_directory() . '/app/Database/load_database.php';

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );    