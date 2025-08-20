<?php
/**
 * Autoloader Class
 * Automatically load classes based on namespace and file structure
 */
class HonorsAutoloader {
    
    private static $instance = null;
    private $base_path;
    private $class_map = [];
    private $scanned_dirs = [];
    private $cache_enabled = true;
    private $cache_file;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->base_path = get_template_directory();
        $this->cache_file = $this->base_path . '/cache/class-map.php';
        
        $this->registerQueryVars();
        
        $this->initClassMap();
        $this->loadClassMap();
        spl_autoload_register([$this, 'autoload']);
    }
    
    /**
     * Initialize class map for autoloading
     */
    private function initClassMap() {

        $this->class_map = [
            // Core MVC Classes
            'HonorsApp' => '/framework/Core/App.php',
            'Router' => '/framework/Core/Router.php',
            'RouteBuilder' => '/framework/Core/RouteBuilder.php',
            'View' => '/framework/Core/View.php',
            'ServiceContainer' => '/framework/Core/ServiceContainer.php',
            'BaseController' => '/framework/Controllers/BaseController.php',
            
            // Framework Services
            'HonorsEnqueue' => '/framework/Services/enqueue.php',
            'HonorsThemeSetup' => '/framework/Services/theme-setup.php',
            'HonorsSecurity' => '/framework/Services/security.php',
            'HonorsCSSManager' => '/framework/Services/css-manager.php',
            'HonorsJSManager' => '/framework/Services/js-manager.php',
            'HonorsAutoloader' => '/framework/Services/autoloader.php',
            'HonorsAdminCache' => '/framework/Services/admin-cache.php',
            'HonorsAjaxLoader' => '/framework/Services/ajax-loader.php',
            'HonorsUrlRewrite' => '/framework/Services/url-rewrite.php',
        ];
    }
    
    /**
     * Load class map from cache or scan directories
     */
    private function loadClassMap() {
        if ($this->cache_enabled && $this->loadCachedClassMap()) {
            return;
        }
        
        // Scan directories if cache not available
        $this->scanControllers();
        $this->cacheClassMap();
    }
    
    /**
     * Load cached class map
     * @return bool True if cache loaded successfully
     */
    private function loadCachedClassMap() {
        if (!file_exists($this->cache_file)) {
            return false;
        }
        
        $cached_map = include $this->cache_file;
        
        if (is_array($cached_map)) {
            $this->class_map = array_merge($this->class_map, $cached_map);
            return true;
        }
        
        return false;
    }
    
    /**
     * Cache class map for better performance
     */
    private function cacheClassMap() {
        if (!$this->cache_enabled) {
            return;
        }
        
        // Create cache directory if not exists
        $cache_dir = dirname($this->cache_file);
        if (!is_dir($cache_dir)) {
            wp_mkdir_p($cache_dir);
        }
        
        // Cache only scanned classes (not core classes)
        $scanned_classes = [];
        foreach ($this->class_map as $class => $path) {
            if (strpos($path, '/app/Controllers/') === 0) {
                $scanned_classes[$class] = $path;
            }
        }
        
        $cache_content = "<?php\nreturn " . var_export($scanned_classes, true) . ";\n";
        file_put_contents($this->cache_file, $cache_content);
    }
    
    /**
     * Clear cache
     */
    public function clearCache() {
        if (file_exists($this->cache_file)) {
            unlink($this->cache_file);
        }
    }
    
    /**
     * Automatically scan and register all controllers
     */
    private function scanControllers() {
        $controllers_dir = $this->base_path . '/app/Controllers/';
        
        if (is_dir($controllers_dir)) {
            $files = glob($controllers_dir . '*.php');
            
            foreach ($files as $file) {
                $class_name = basename($file, '.php');
                $relative_path = '/app/Controllers/' . basename($file);
                
                $this->class_map[$class_name] = $relative_path;
            }
        }
        
        $this->scanned_dirs[] = 'controllers';
    }
    
    /**
     * Scan additional directories for classes
     * @param string $dir_path Directory path relative to theme root
     * @param string $dir_name Directory name for tracking
     */
    public function scanDirectory($dir_path, $dir_name = '') {
        $full_dir = $this->base_path . $dir_path;
        
        if (is_dir($full_dir)) {
            $files = glob($full_dir . '/*.php');
            
            foreach ($files as $file) {
                $class_name = basename($file, '.php');
                $relative_path = $dir_path . '/' . basename($file);
                
                $this->class_map[$class_name] = $relative_path;
            }
            
            if ($dir_name) {
                $this->scanned_dirs[] = $dir_name;
            }
        }
    }
    
    /**
     * Autoload function
     * @param string $class_name Class name to load
     */
    public function autoload($class_name) {
        // Check if class exists in class map
        if (isset($this->class_map[$class_name])) {
            $file_path = $this->base_path . $this->class_map[$class_name];
            
            if (file_exists($file_path)) {
                require_once $file_path;
                return true;
            }
        }
        
        // Try to autoload based on PSR-4 style naming
        $file_path = $this->findClassFile($class_name);
        if ($file_path && file_exists($file_path)) {
            require_once $file_path;
            return true;
        }
        
        return false;
    }
    
    /**
     * Find class file based on PSR-4 style naming
     * @param string $class_name Class name
     * @return string|false File path or false if not found
     */
    private function findClassFile($class_name) {
        // Handle namespace App\ to app/ directory mapping
        if (strpos($class_name, 'App\\') === 0) {
            $relative_class = substr($class_name, 4); // Remove 'App\'
            $file_path = $this->base_path . '/app/' . str_replace('\\', '/', $relative_class) . '.php';
            
            if (file_exists($file_path)) {
                return $file_path;
            }
        }        
        return false;
    }
    
    /**
     * Add class to autoload map
     * @param string $class_name Class name
     * @param string $file_path File path relative to theme root
     */
    public function addClass($class_name, $file_path) {
        $this->class_map[$class_name] = $file_path;
    }
    
    /**
     * Get all registered classes
     * @return array Class map
     */
    public function getClassMap() {
        return $this->class_map;
    }
    
    /**
     * Get scanned directories
     * @return array Scanned directories
     */
    public function getScannedDirs() {
        return $this->scanned_dirs;
    }
    
    /**
     * Check if class is loaded
     * @param string $class_name Class name
     * @return bool True if class exists
     */
    public function isClassLoaded($class_name) {
        return class_exists($class_name);
    }
    
    /**
     * Get all available controllers
     * @return array Array of controller class names
     */
    public function getControllers() {
        $controllers = [];
        foreach ($this->class_map as $class_name => $file_path) {
            if (strpos($file_path, '/app/Controllers/') === 0) {
                $controllers[] = $class_name;
            }
        }
        return $controllers;
    }
    
    /**
     * Enable/disable caching
     * @param bool $enabled Cache enabled
     */
    public function setCacheEnabled($enabled) {
        $this->cache_enabled = $enabled;
    }
    
    /**
     * Get cache status
     * @return bool Cache enabled
     */
    public function isCacheEnabled() {
        return $this->cache_enabled;
    }
        
    /**
     * Register custom query vars
     * Called early in autoloader constructor
     */
    private function registerQueryVars() {
        add_filter('query_vars', function($query_vars) {
            $query_vars[] = 'subpage';
            $query_vars[] = 'filter'; 
            $query_vars[] = 'action';
            $query_vars[] = 'section';
            $query_vars[] = 'page';
            return $query_vars;
        });
    }
} 