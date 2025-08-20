<?php
/**
 * AJAX Loader Class
 * Automatically discover and load all AJAX classes from includes/ajax directory
 */
class HonorsAjaxLoader {
    private static $instance = null;
    private $ajax_classes = [];
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Also register for init hook as backup
        add_action('init', [$this, 'loadAjaxClasses']);
    }
    
    /**
     * Load all AJAX classes from includes/ajax directory
     */
    public function loadAjaxClasses() {
        $ajax_dir = get_template_directory() . '/app/Ajax/';
        
        if (!is_dir($ajax_dir)) {
            return;
        }
        
        $files = glob($ajax_dir . '*.php');
        
        foreach ($files as $file) {
            $class_name = basename($file, '.php');

            // Load the file first, then check if class exists
            require_once $file;
            
            if (class_exists($class_name) && $this->isAjaxClass($class_name)) {
                try {
                    $this->ajax_classes[] = new $class_name();
                } catch (Exception $e) {
                    // Log only if debug mode is enabled
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log("Failed to instantiate AJAX class {$class_name}: " . $e->getMessage());
                    }
                }
            }
        }
    }
    
    /**
     * Check if a class is an AJAX class (has AJAX actions)
     */
    private function isAjaxClass($class_name) {
        $reflection = new ReflectionClass($class_name);
        
        // Check if class has AJAX action methods
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getName() !== '__construct' && !$method->isStatic()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get loaded AJAX classes
     */
    public function getLoadedClasses() {
        return $this->ajax_classes;
    }
}
