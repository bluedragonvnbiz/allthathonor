<?php
/**
 * Helper Functions
 * General utility functions for the theme
 */

/**
 * Get current user ID
 * @return int User ID
 */
function honors_get_current_user_id() {
    return get_current_user_id();
}

/**
 * Check if user is logged in
 * @return bool True if user is logged in
 */
function honors_is_user_logged_in() {
    return is_user_logged_in();
}

/**
 * Get theme URL
 * @return string Theme URL
 */
function honors_get_theme_url() {
    return get_template_directory_uri();
}

/**
 * Get theme directory
 * @return string Theme directory path
 */
function honors_get_template_dir() {
    return get_template_directory();
}

/**
 * Autoloader Helper Functions
 */

/**
 * Add a new class to autoloader
 * @param string $class_name Class name
 * @param string $file_path File path relative to theme root
 */
function honors_autoload_class($class_name, $file_path) {
    $autoloader = HonorsAutoloader::getInstance();
    $autoloader->addClass($class_name, $file_path);
}

/**
 * Check if class is loaded
 * @param string $class_name Class name
 * @return bool True if class exists
 */
function honors_class_loaded($class_name) {
    $autoloader = HonorsAutoloader::getInstance();
    return $autoloader->isClassLoaded($class_name);
}

/**
 * Get all registered classes
 * @return array Class map
 */
function honors_get_class_map() {
    $autoloader = HonorsAutoloader::getInstance();
    return $autoloader->getClassMap();
}

/**
 * Load class manually if needed
 * @param string $class_name Class name
 * @return bool True if class loaded successfully
 */
function honors_load_class($class_name) {
    if (!honors_class_loaded($class_name)) {
        $autoloader = HonorsAutoloader::getInstance();
        return $autoloader->autoload($class_name);
    }
    return true;
}

/**
 * Get all available controllers
 * @return array Array of controller class names
 */
function honors_get_controllers() {
    $autoloader = HonorsAutoloader::getInstance();
    return $autoloader->getControllers();
}

/**
 * Get scanned directories
 * @return array Scanned directories
 */
function honors_get_scanned_dirs() {
    $autoloader = HonorsAutoloader::getInstance();
    return $autoloader->getScannedDirs();
}

/**
 * Route Helper Functions
 * Provides wp_route() function for fluent route definition
 */

 if (!function_exists('wp_route')) {
    /**
     * Route helper function
     * Usage: wp_route('management')->action('index@ManagementController')->admin()
     * 
     * @param string $path
     * @return RouteBuilder
     */
    function wp_route($path) {
        return Router::define($path);
    }
}