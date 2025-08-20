<?php
/**
 * JavaScript Manager Class
 * Handle JS registration and loading for different pages
 */
class HonorsJSManager {
    private static $instance = null;
    private $registered_js = [];
    private $page_js = [];
    private $common_js = [];
    private $default_js_path = 'assets/js';
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueJS']);
    }

    /**
     * Register JS for a specific page
     * @param string $page_name Page identifier
     * @param array $js_files Array of JS files (without .js extension)
     */
    public function registerPageJS($page_name, $js_files) {
        $this->page_js[$page_name] = $js_files;
    }
    
    /**
     * Add JS files to current page with custom path support
     * @param array $js_files Array of JS files (can include full paths or relative paths)
     * @param string $base_path Optional base path (default: assets/js)
     * @param array $dependencies Optional dependencies array
     */
    public function addJS($js_files, $base_path = null, $dependencies = ['jquery']) {
        // If base_path is provided, prepend it to all JS files
        if ($base_path !== null) {
            $js_files = array_map(function($js_file) use ($base_path) {
                return $base_path . '/' . $js_file;
            }, $js_files);
        }
        
        // Store JS files with their dependencies
        foreach ($js_files as $js_file) {
            $this->registered_js[] = [
                'file' => $js_file,
                'dependencies' => $dependencies
            ];
        }
    }
    
    /**
     * Add single JS file with custom path
     * @param string $js_file JS file path (can be full path or relative)
     * @param string $base_path Optional base path
     * @param array $dependencies Optional dependencies array
     */
    public function addSingleJS($js_file, $base_path = null, $dependencies = ['jquery']) {
        if ($base_path !== null) {
            $js_file = $base_path . '/' . $js_file;
        }
        
        $this->registered_js[] = [
            'file' => $js_file,
            'dependencies' => $dependencies
        ];
    }
    
    /**
     * Get all JS files for current page
     * @return array Array of JS files with dependencies
     */
    public function getCurrentPageJS() {
        return array_merge($this->common_js, $this->registered_js);
    }
    
    /**
     * Enqueue JS files with smart path resolution
     */
    public function enqueueJS() {
        $js_files = $this->getCurrentPageJS();
        
        foreach ($js_files as $js_data) {
            $js_file = is_array($js_data) ? $js_data['file'] : $js_data;
            $dependencies = is_array($js_data) ? $js_data['dependencies'] : ['jquery'];
            
            $handle = 'honors-' . str_replace(['/', '\\'], '-', $js_file);
            
            // Determine if it's a full path or relative path
            if (strpos($js_file, 'http') === 0 || strpos($js_file, '//') === 0) {
                // External JS (CDN, etc.)
                $file_path = $js_file;
                $version = null;
            } else {
                // Local JS file
                if (strpos($js_file, '/') === 0) {
                    // Absolute path from theme root
                    $file_path = get_template_directory_uri() . $js_file . '.js';
                    $local_path = get_template_directory() . $js_file . '.js';
                } else {
                    // Relative path (default: assets/js)
                    $file_path = get_template_directory_uri() . '/' . $this->default_js_path . '/' . $js_file . '.js';
                    $local_path = get_template_directory() . '/' . $this->default_js_path . '/' . $js_file . '.js';
                }
                
                // Get file modification time for versioning
                $version = file_exists($local_path) ? filemtime($local_path) : '1.0.0';
            }
            
            wp_enqueue_script($handle, $file_path, $dependencies, $version, true);
        }
    }
    
    /**
     * Set default JS path
     * @param string $path Default path for JS files
     */
    public function setDefaultJSPath($path) {
        $this->default_js_path = $path;
    }
    
    /**
     * Get default JS path
     * @return string Current default JS path
     */
    public function getDefaultJSPath() {
        return $this->default_js_path;
    }
    
    /**
     * Clear registered JS for current request
     */
    public function clearJS() {
        $this->registered_js = [];
    }
} 