<?php
/**
 * Main Application Class
 * Bootstrap MVC application
 */
class HonorsApp {
    private static $instance = null;
    public $router;
    public $view;
    public $container;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->container = ServiceContainer::getInstance();
    }
    
    public function init() {
        // Initialize services first
        $this->container->initializeServices();
        
        // Create Router and View when needed
        if (!$this->router) {
            $this->router = Router::getInstance();
        }
        if (!$this->view) {
            $this->view = new View();
        }
    }
    
    public function run() {
        // Ensure init was called
        if (!$this->router || !$this->view) {
            $this->init();
        }
        
        // Handle the current route
        $this->router->route();
    }
    
    /**
     * Get service from container
     * @param string $name Service name
     * @return object
     */
    public function service($name) {
        return $this->container->get($name);
    }
    
    /**
     * Get configuration
     * @param string $key Config key
     * @return mixed
     */
    public function config($key = null) {
        $config = include get_template_directory() . '/config/app.php';
        
        if ($key === null) {
            return $config;
        }
        
        return $config[$key] ?? null;
    }
} 