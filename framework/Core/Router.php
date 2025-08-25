<?php
/**
 * Router Class
 * Handle routing requests to appropriate controllers
 */

// Load RouteBuilder class
require_once get_template_directory() . '/framework/Core/RouteBuilder.php';

class Router {
    private static $instance = null;
    private static $routes = [];
    private static $route_builder;
    private static $routes_loaded = false;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        if (empty(self::$routes)) {
            include get_template_directory() . '/config/routes.php';
            $this->initializeUrlRewrite();
            add_action('wp_loaded', [$this, 'initializeUrlRewrite'], 20);
        }
    }
    
    /**
     * Route Builder - Fluent API for defining routes
     * Usage: wp_route('management')->action('index@ManagementController')->setCapability('manage_options')
     */
    public static function define($path) {
        return new RouteBuilder($path);
    }
    
    /**
     * Register a route
     * @param string $path
     * @param array $config
     */
    public static function registerRoute($path, $config) {
        self::$routes[$path] = $config;
    }
    
    /**
     * Get all registered routes
     * @return array
     */
    public static function getRoutes() {
        return self::$routes;
    }
    
    /**
     * Initialize HonorsUrlRewrite after routes are loaded
     * Public method that can be called by wp_loaded hook
     */
    public function initializeUrlRewrite() {
        // Ensure we have routes loaded
        if (empty(self::$routes)) {
            return;
        }
        
        // Get HonorsUrlRewrite instance from ServiceContainer
        try {
            $url_rewrite = HonorsApp::getInstance()->container->get('url_rewrite');
            if ($url_rewrite && method_exists($url_rewrite, 'addRewriteRules')) {
                $url_rewrite->addRewriteRules();
            } else {
            }
        } catch (Exception $e) {
            // do nothing
        }
    }
    
    /**
     * Set layout for View
     * @param string $layout
     */
    private function setLayout($layout) {
        $view = HonorsApp::getInstance()->view;
        if ($view) {
            $view->layout($layout);
        }
    }
    
    public function route() {
        // Check for configured routes first
        $current_route = $this->getCurrentRoute();
        
        if ($current_route) {
            $this->handleConfiguredRoute($current_route);
            return;
        }
    }
    
    /**
     * Get current route from config
     * @return array|null
     */
    public function getCurrentRoute() {
        // Debug
        error_log('Current Routes: ' . print_r(self::$routes, true));
        error_log('Request URI: ' . $_SERVER['REQUEST_URI']);
        error_log('Query Vars: ' . print_r($GLOBALS['wp_query']->query_vars, true));

        // Try to get route from query var first (set by our rewrite rules)
        $honors_route = get_query_var('honors_route');
        error_log('Honors Route: ' . $honors_route);
        
        if ($honors_route && isset(self::$routes[$honors_route])) {
            error_log('Found route config: ' . print_r(self::$routes[$honors_route], true));
            return self::$routes[$honors_route];
        }

        // Fallback to request URI parsing
        $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        error_log('Parsed Request URI: ' . $request_uri);
        
        // Try exact match first
        if (isset(self::$routes[$request_uri])) {
            error_log('Found route by exact match: ' . $request_uri);
            return self::$routes[$request_uri];
        }

        // Try without trailing slash
        $alt_uri = rtrim($request_uri, '/');
        if ($alt_uri !== $request_uri && isset(self::$routes[$alt_uri])) {
            error_log('Found route without trailing slash: ' . $alt_uri);
            return self::$routes[$alt_uri];
        }

        // Check for home page last
        if (empty($request_uri) || is_home() || is_front_page()) {
            error_log('Homepage detected');
            return isset(self::$routes['home']) ? self::$routes['home'] : null;
        }

        error_log('No route found for: ' . $request_uri);
        return null;
    }
    
    /**
     * Handle configured route
     * @param array $route_config
     */
    private function handleConfiguredRoute($route_config) {
        error_log('Handling route config: ' . print_r($route_config, true));
        
        // Create request data
        $request = [
            'path' => $_SERVER['REQUEST_URI'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'query' => $_GET,
            'post' => $_POST,
            'route_config' => $route_config
        ];
        
        // Build middleware chain
        $middleware = isset($route_config['middleware']) ? $route_config['middleware'] : [];
        
        // Add default middleware for capability checks
        if (isset($route_config['capability'])) {
            array_unshift($middleware, function($request, $next) use ($route_config) {
                if (!current_user_can($route_config['capability'])) {
                    wp_die(__('You do not have sufficient permissions to access this page.'));
                }
                return $next($request);
            });
        }
        
        // Build the middleware pipeline
        $pipeline = $this->buildMiddlewarePipeline($middleware, function($request) use ($route_config) {
            // Parse route string (method@controller) and preserve other config
            if (isset($route_config['action'])) {
                $parsed = $this->parseRouteString($route_config['action']);
                $route_config = array_merge($route_config, $parsed);
            }
            
            // Set layout for View
            if (isset($route_config['layout'])) {
                $this->setLayout($route_config['layout']);
            }
            
            // Create controller
            $controller_class = $route_config['controller'];
            $controller = new $controller_class();
            
            // Call the method directly
            return $this->handleMainPage($controller, $route_config);
        });
        
        // Execute the pipeline
        return $pipeline($request);
    }
    
    /**
     * Build middleware pipeline
     * @param array $middleware Array of middleware
     * @param callable $core Core handler
     * @return callable
     */
    private function buildMiddlewarePipeline($middleware, callable $core) {
        return array_reduce(array_reverse($middleware), function($next, $middleware) {
            return function($request) use ($next, $middleware) {
                if (is_string($middleware)) {
                    $middleware = new $middleware();
                }
                
                if (is_callable($middleware)) {
                    return $middleware($request, $next);
                }
                
                if (method_exists($middleware, 'handle')) {
                    return $middleware->handle($request, $next);
                }
                
                throw new Exception('Invalid middleware type');
            };
        }, $core);
    }
    
    /**
     * Handle subpage
     * @param object $controller
     * @param array $route_config
     * @param string $subpage
     */
    private function handleSubpage($controller, $route_config, $subpage) {
        $subpage_config = $route_config['subpages'][$subpage];
        $method = $subpage_config['method'];
        
        if (method_exists($controller, $method)) {
            $controller->$method();
        } else {
            // Fallback to main page
            $this->handleMainPage($controller, $route_config);
        }
    }
    
    /**
     * Parse route string in format "method@controller"
     * @param string $route_string
     * @return array
     */
    private function parseRouteString($route_string) {
        error_log('Parsing route string: ' . $route_string);
        
        $parts = explode('@', $route_string);
        if (count($parts) === 2) {
            $result = [
                'method' => $parts[0],
                'controller' => $parts[1]
            ];
            error_log('Parsed route parts: ' . print_r($result, true));
            return $result;
        }
        
        $result = [
            'method' => 'index',
            'controller' => $route_string
        ];
        error_log('Using default route parts: ' . print_r($result, true));
        return $result;
    }
    
    /**
     * Handle main page
     * @param object $controller
     * @param array $route_config
     */
    private function handleMainPage($controller, $route_config) {
        if (isset($route_config['method'])) {
            $method = $route_config['method'];
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                $controller->index();
            }
        } else {
            $controller->index();
        }
    }
} 