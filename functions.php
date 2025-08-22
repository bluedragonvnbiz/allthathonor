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