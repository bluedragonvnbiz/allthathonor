<?php
/**
 * Route Builder Class
 * Provides fluent API for defining routes
 */
class RouteBuilder {
    private $path;
    private $action;
    private $capability = null;
    private $layout = 'main';
    private $prefix = '';
    private $middleware = [];
    private static $currentGroup = null;
    
    /**
     * Constructor
     * @param string $path
     */
    public function __construct($path = null) {
        if ($path) {
            $this->path = $path;
        }
        
        // Apply group settings if in a group context
        if (self::$currentGroup) {
            $this->applyGroupSettings(self::$currentGroup);
        }
    }
    
    /**
     * Create a new route group
     * @param array $settings Group settings
     * @param callable $callback Group definition callback
     */
    public static function group($settings, $callback) {
        $previousGroup = self::$currentGroup;
        
        // Merge with previous group settings if nested
        if ($previousGroup) {
            $settings = self::mergeGroupSettings($previousGroup, $settings);
        }
        
        self::$currentGroup = $settings;
        
        // Execute the group definition
        call_user_func($callback);
        
        // Restore previous group context
        self::$currentGroup = $previousGroup;
    }
    
    /**
     * Merge group settings for nested groups
     * @param array $previous Previous group settings
     * @param array $current Current group settings
     * @return array
     */
    private static function mergeGroupSettings($previous, $current) {
        return [
            'prefix' => trim($previous['prefix'] . '/' . ($current['prefix'] ?? ''), '/'),
            'middleware' => array_merge($previous['middleware'] ?? [], $current['middleware'] ?? []),
            'layout' => $current['layout'] ?? $previous['layout'] ?? 'main',
            'capability' => $current['capability'] ?? $previous['capability'] ?? null,
        ];
    }
    
    /**
     * Apply group settings to the current route
     * @param array $settings
     */
    private function applyGroupSettings($settings) {
        if (isset($settings['prefix'])) {
            $this->prefix = $settings['prefix'];
        }
        
        if (isset($settings['middleware'])) {
            $this->middleware = array_merge($this->middleware, $settings['middleware']);
        }
        
        if (isset($settings['layout'])) {
            $this->layout = $settings['layout'];
        }
        
        if (isset($settings['capability'])) {
            $this->capability = $settings['capability'];
        }
        

    }
    
    /**
     * Set route path
     * @param string $path
     * @return $this
     */
    public function path($path) {
        $this->path = $path;
        return $this;
    }
    
    /**
     * Set controller action
     * @param string $action Format: method@controller
     * @return $this
     */
    public function action($action) {
        $this->action = $action;
        return $this;
    }
    
    /**
     * Add middleware to the route
     * @param string|array $middleware
     * @return $this
     */
    public function middleware($middleware) {
        if (is_array($middleware)) {
            $this->middleware = array_merge($this->middleware, $middleware);
        } else {
            $this->middleware[] = $middleware;
        }
        return $this;
    }
    
    /**
     * Set required capability
     * @param string $capability
     * @return $this
     */
    public function setCapability($capability) {
        $this->capability = $capability;
        return $this;
    }
    
    /**
     * Set admin route (capability: manage_options)
     * @return $this
     */
    public function admin() {
        $this->capability = 'manage_options';
        $this->layout = 'admin';
        $this->middleware[] = 'AdminMiddleware';
        return $this;
    }
    
    /**
     * Set auth route (requires login)
     * @return $this
     */
    public function auth() {
        $this->layout = 'main';
        $this->middleware[] = 'AuthMiddleware';
        return $this;
    }
    
    /**
     * Set public route (no auth required)
     * @return $this
     */
    public function public() {
        $this->layout = 'main';
        return $this;
    }
    
    /**
     * Build route configuration
     * @return array
     */
    public function build() {
        // Build the full path including prefix
        $fullPath = $this->prefix ? trim($this->prefix . '/' . $this->path, '/') : $this->path;
        
        $config = [
            'path' => $fullPath,
            'action' => $this->action,
            'layout' => $this->layout,
            'middleware' => array_unique($this->middleware)
        ];
        
        if ($this->capability !== null && $this->capability !== 'read') {
            $config['capability'] = $this->capability;
        }
        

        
        return $config;
    }
    
    /**
     * Register route to Router
     * @return void
     */
    public function register() {
        // Build the full path including prefix
        $fullPath = $this->prefix ? trim($this->prefix . '/' . $this->path, '/') : $this->path;
        Router::registerRoute($fullPath, $this->build());
    }
    
    /**
     * Get route as string for direct assignment
     * @return string
     */
    public function __toString() {
        return $this->action;
    }
}