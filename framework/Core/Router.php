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
        // Check for home page first
        if (is_home() || is_front_page()) {
            return isset(self::$routes['home']) ? self::$routes['home'] : null;
        }
        
        // Get current page and subpage
        $current_page = get_query_var('pagename');
        $subpage = get_query_var('subpage');
        
        // Manual URL parsing as fallback
        if (!$current_page || !$subpage) {
            $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            $path_parts = explode('/', $request_uri);
            
            if (!empty($path_parts[0])) {
                if (!$current_page) {
                    $current_page = $path_parts[0];
                }
                if (!$subpage && !empty($path_parts[1])) {
                    $subpage = $path_parts[1];
                }
            }
        }
        
        if (!$current_page) {
            return null;
        }
        
        // Check for subpage route FIRST
        if ($subpage) {
            $full_route = $current_page . '/' . $subpage;
            if (isset(self::$routes[$full_route])) {
                return self::$routes[$full_route];
            }
            
            wp_redirect(home_url(), 301);
            exit;
        }
        
        // Check for exact parent page match (only if no subpage)
        if (isset(self::$routes[$current_page])) {
            return self::$routes[$current_page];
        }
        
        return null;
    }
    
    /**
     * Handle configured route
     * @param array $route_config
     */
    private function handleConfiguredRoute($route_config) {
        // Check if login is required FIRST
        if (isset($route_config['require_login']) && $route_config['require_login']) {
            if (!is_user_logged_in()) {
                wp_redirect(home_url('/login'));
                exit;
            }
        }
        
        // Check capability AFTER login check
        if (isset($route_config['capability'])) {
            if (!current_user_can($route_config['capability'])) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
        }
        
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
        $this->handleMainPage($controller, $route_config);
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
        $parts = explode('@', $route_string);
        if (count($parts) === 2) {
            return [
                'method' => $parts[0],
                'controller' => $parts[1]
            ];
        }
        return [
            'method' => 'index',
            'controller' => $route_string
        ];
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
    
    /**
     * Handle WordPress conditional routes
     */
    private function handleWordPressRoutes() {
        if (is_category()) {
            $controller = new CategoryController();
            $controller->archive();
        }
        elseif (is_tag()) {
            $controller = new TagController();
            $controller->archive();
        }
        elseif (is_tax()) {
            $controller = new TaxonomyController();
            $controller->archive();
        }
        elseif (is_single()) {
            $controller = new PostController();
            $controller->single();
        }
        elseif (is_archive()) {
            $controller = new ArchiveController();
            $controller->archive();
        }
        elseif (is_search()) {
            $controller = new SearchController();
            $controller->search();
        }
        elseif (is_404()) {
            $controller = new ErrorController();
            $controller->notFound();
        }
        else {
            $controller = new DefaultController();
            $controller->index();
        }
    }
    

} 