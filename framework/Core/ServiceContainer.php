<?php
/**
 * Service Container
 * Manage framework dependencies and services
 */
class ServiceContainer {
    private static $instance = null;
    private $services = [];
    private $singletons = [];
    private $config;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->config = include get_template_directory() . '/config/services.php';
        $this->registerDefaultServices();
    }
    
    /**
     * Register default services
     */
    private function registerDefaultServices() {
        foreach ($this->config['services'] as $name => $service) {
            $this->register(
                $name, 
                $service['class'], 
                $service['priority'] ?? 10,
                $service['singleton'] ?? false
            );
        }
    }
    
    /**
     * Register a service
     * @param string $name Service name
     * @param string $class Class name
     * @param int $priority Priority
     * @param bool $singleton Whether service is singleton
     */
    public function register($name, $class, $priority = 10, $singleton = false) {
        $this->services[$name] = [
            'class' => $class,
            'priority' => $priority,
            'singleton' => $singleton
        ];
    }
    
    /**
     * Get a service
     * @param string $name Service name
     * @return object Service instance
     */
    public function get($name) {
        if (isset($this->singletons[$name])) {
            return $this->singletons[$name];
        }
        
        if (!isset($this->services[$name])) {
            throw new Exception("Service '{$name}' not found");
        }
        
        $service = $this->services[$name];
        $class = $service['class'];
        
        if (!class_exists($class)) {
            throw new Exception("Class '{$class}' not found");
        }
        
        // Handle singleton services
        if ($service['singleton'] && method_exists($class, 'getInstance')) {
            $instance = $class::getInstance();
        } else {
            $instance = new $class();
        }
        
        $this->singletons[$name] = $instance;
        
        return $instance;
    }
    
    /**
     * Check if service exists
     * @param string $name Service name
     * @return bool
     */
    public function has($name) {
        return isset($this->services[$name]);
    }
    
    /**
     * Initialize all services
     */
    public function initializeServices() {
        // Sort services by priority
        uasort($this->services, function($a, $b) {
            return $a['priority'] - $b['priority'];
        });
        
        foreach ($this->services as $name => $service) {
            // Skip admin-only services on frontend
            if (isset($service['admin_only']) && $service['admin_only'] && !is_admin()) {
                continue;
            }
            
            try {
                $this->get($name);
            } catch (Exception $e) {
                // Log only actual errors, not debug info
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("Failed to initialize service '{$name}': " . $e->getMessage());
                }
            }
        }
    }
    
    /**
     * Get all registered services
     * @return array
     */
    public function getServices() {
        return $this->services;
    }
    
    /**
     * Get configuration
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }
}
