<?php
/**
 * CSS Manager Class
 * Handle CSS registration and loading for different pages
 */
class HonorsCSSManager {
    private static $instance = null;
    private $registered_css = [];
    private $page_css = [];
    private $common_css = [];
    private $default_css_path = 'assets/css';
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueCSS']);
    }

    /**
     * Register CSS for a specific page
     * @param string $page_name Page identifier
     * @param array $css_files Array of CSS files (without .css extension)
     */
    public function registerPageCSS($page_name, $css_files) {
        $this->page_css[$page_name] = $css_files;
    }
    
    /**
     * Add CSS files to current page with custom path support
     * @param array $css_files Array of CSS files (can include full paths or relative paths)
     * @param string $base_path Optional base path (default: assets/css)
     */
    public function addCSS($css_files, $base_path = null) {
        // If base_path is provided, prepend it to all CSS files
        if ($base_path !== null) {
            $css_files = array_map(function($css_file) use ($base_path) {
                return $base_path . '/' . $css_file;
            }, $css_files);
        }
        
        $this->registered_css = array_merge($this->registered_css, $css_files);
    }
    
    /**
     * Add single CSS file with custom path
     * @param string $css_file CSS file path (can be full path or relative)
     * @param string $base_path Optional base path
     */
    public function addSingleCSS($css_file, $base_path = null) {
        if ($base_path !== null) {
            $css_file = $base_path . '/' . $css_file;
        }
        
        $this->registered_css[] = $css_file;
    }
    
    /**
     * Get all CSS files for current page
     * @return array Array of CSS files
     */
    public function getCurrentPageCSS() {
        return array_merge($this->common_css, $this->registered_css);
    }
    
    /**
     * Enqueue CSS files with smart path resolution
     */
    public function enqueueCSS() {
        $css_files = $this->getCurrentPageCSS();
        
        foreach ($css_files as $css_file) {
            $handle = 'honors-' . str_replace(['/', '\\'], '-', $css_file);
            
            // Determine if it's a full path or relative path
            if (strpos($css_file, 'http') === 0 || strpos($css_file, '//') === 0) {
                // External CSS (CDN, etc.)
                $file_path = $css_file;
                $version = null;
            } else {
                // Local CSS file
                if (strpos($css_file, '/') === 0) {
                    // Absolute path from theme root
                    $file_path = get_template_directory_uri() . $css_file . '.css';
                    $local_path = get_template_directory() . $css_file . '.css';
                } else {
                    // Relative path (default: assets/css)
                    $file_path = get_template_directory_uri() . '/' . $this->default_css_path . '/' . $css_file . '.css';
                    $local_path = get_template_directory() . '/' . $this->default_css_path . '/' . $css_file . '.css';
                }
                
                // Get file modification time for versioning
                $version = file_exists($local_path) ? filemtime($local_path) : '1.0.0';
            }
            
            wp_enqueue_style($handle, $file_path, [], $version);
        }
    }
    
    /**
     * Set default CSS path
     * @param string $path Default path for CSS files
     */
    public function setDefaultCSSPath($path) {
        $this->default_css_path = $path;
    }
    
    /**
     * Get default CSS path
     * @return string Current default CSS path
     */
    public function getDefaultCSSPath() {
        return $this->default_css_path;
    }
    
    /**
     * Clear registered CSS for current request
     */
    public function clearCSS() {
        $this->registered_css = [];
    }
} 