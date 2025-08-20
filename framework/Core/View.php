<?php
/**
 * View Helper Class
 * Handle loading layouts and partials
 */
class View {
    private $layout_name = 'main';
    private $data = [];
    private $css_manager;
    private $js_manager;
    
    public function __construct() {
        $this->css_manager = HonorsCSSManager::getInstance();
        $this->js_manager = HonorsJSManager::getInstance();
    }
    
    public function layout($name) {
        $this->layout_name = $name;
    }
    
    /**
     * Add CSS files for current page
     * @param array|string $css_files Array of CSS files or single CSS file (without .css extension)
     * @param string $base_path Optional base path
     */
    public function addCSS($css_files, $base_path = null) {
        if (is_string($css_files)) {
            $this->css_manager->addSingleCSS($css_files, $base_path);
        } else {
            $this->css_manager->addCSS($css_files, $base_path);
        }
    }
    
    /**
     * Add JS files for current page
     * @param array|string $js_files Array of JS files or single JS file (without .js extension)
     * @param string $base_path Optional base path
     * @param array $dependencies Optional dependencies array
     */
    public function addJS($js_files, $base_path = null, $dependencies = ['jquery']) {
        if (is_string($js_files)) {
            $this->js_manager->addSingleJS($js_files, $base_path, $dependencies);
        } else {
            $this->js_manager->addJS($js_files, $base_path, $dependencies);
        }
    }
    
    /**
     * Add single JS file
     * @param string $js_file JS file path
     * @param string $base_path Optional base path
     * @param array $dependencies Optional dependencies array
     */
    public function addSingleJS($js_file, $base_path = null, $dependencies = ['jquery']) {
        $this->js_manager->addSingleJS($js_file, $base_path, $dependencies);
    }
    
    /**
     * Load partial with support for subdirectories
     * @param string $name Partial name (can include subdirectory like 'home/banner')
     * @param array $data Data to pass to partial
     */
    public function partial($name, $data = []) {
        $this->data = array_merge($this->data, $data);
        extract($this->data);
        
        // Check if partial exists in subdirectory
        $partial_path = get_template_directory() . "/app/Views/partials/{$name}.php";
        
        if (file_exists($partial_path)) {
            include $partial_path;
        } else {
            // Fallback to common directory
            $common_path = get_template_directory() . "/app/Views/partials/common/{$name}.php";
            if (file_exists($common_path)) {
                include $common_path;
            } else {
                echo "<!-- Partial not found: {$name} -->";
            }
        }
    }
    
    /**
     * Render view with support for subdirectories
     * @param string $view_name View name (can include subdirectory like 'default/index')
     * @param array $data Data to pass to view
     */
    public function render($view_name, $data = []) {
        $this->data = array_merge($this->data, $data);
        extract($this->data);
        
        // Check if view contains subdirectory
        if (strpos($view_name, '/') !== false) {
            // View is in subdirectory (e.g., 'default/index', 'categories/archive')
            $view_path = get_template_directory() . "/app/Views/{$view_name}.php";
        } else {
            // View is in pages directory (e.g., 'home', 'management')
            $view_path = get_template_directory() . "/app/Views/pages/{$view_name}.php";
        }
        
        // Start output buffering
        ob_start();
        if (file_exists($view_path)) {
            include $view_path;
        } else {
            echo "<!-- View not found: {$view_name} -->";
        }
        $content = ob_get_clean();
        
        // Render layout with content
        $this->renderLayout($content);
    }
    
    private function renderLayout($content) {
        $this->data['content'] = $content;
        extract($this->data);
        include get_template_directory() . "/app/Views/layouts/{$this->layout_name}.php";
    }
} 