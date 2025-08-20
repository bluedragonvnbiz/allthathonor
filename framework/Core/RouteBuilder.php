<?php
/**
 * Route Builder Class
 * Provides fluent API for defining routes
 */
class RouteBuilder {
    private $path;
    private $action;
    private $capability = 'read';
    private $layout = 'main';
    private $require_login = false;
    
    /**
     * Constructor
     * @param string $path
     */
    public function __construct($path = null) {
        if ($path) {
            $this->path = $path;
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
        $this->require_login = true;
        return $this;
    }
    
    /**
     * Set auth route (capability: read + require login)
     * @return $this
     */
    public function auth() {
        $this->capability = 'read';
        $this->layout = 'main';
        $this->require_login = true;
        return $this;
    }
    
    /**
     * Set public route (no capability required)
     * @return $this
     */
    public function public() {
        $this->capability = null;
        $this->layout = 'main';
        return $this;
    }
    
    /**
     * Set login required route (capability: read + user must be logged in)
     * @return $this
     */
    public function loginRequired() {
        $this->capability = 'read';
        $this->layout = 'main';
        $this->require_login = true;
        return $this;
    }
    
    /**
     * Build route configuration
     * @return array
     */
    public function build() {
        $config = [
            'action' => $this->action,
            'layout' => $this->layout
        ];
        
        // Only add capability if it's not null and not the default 'read'
        if ($this->capability !== null && $this->capability !== 'read') {
            $config['capability'] = $this->capability;
        }
        
        // Add require_login if it's true
        if ($this->require_login) {
            $config['require_login'] = true;
        }
        
        return $config;
    }
    
    /**
     * Register route to Router
     * @return void
     */
    public function register() {
        Router::registerRoute($this->path, $this->build());
    }
    
    /**
     * Get route as string for direct assignment
     * @return string
     */
    public function __toString() {
        return $this->action;
    }
}
