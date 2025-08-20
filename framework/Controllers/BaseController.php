<?php
/**
 * Base Controller Class
 * Provides common functionality for all controllers
 */
abstract class BaseController {
    protected $view;
    protected $user;
    protected $user_info;
    protected $is_logged_in;
    protected $page_title;
    
    public function __construct() {
        $this->view = HonorsApp::getInstance()->view;
        $this->user = wp_get_current_user();
        $this->is_logged_in = is_user_logged_in();
        $this->user_info = $this->getUserInfo();
        
        $this->page_title = get_bloginfo('name');
        add_action('wp_head', [$this, 'outputPageTitle'], 1);
    }
    
    /**
     * Get current user information
     * @return array User info array
     */
    protected function getUserInfo() {
        if (!$this->is_logged_in) {
            return [
                'id' => 0,
                'name' => 'Guest',
                'email' => '',
                'role' => 'guest',
                'avatar' => get_avatar_url(0),
                'is_admin' => false
            ];
        }
        
        return [
            'id' => $this->user->ID,
            'name' => $this->user->display_name,
            'email' => $this->user->user_email,
            'role' => $this->user->roles[0] ?? 'subscriber',
            'avatar' => get_avatar_url($this->user->ID),
            'is_admin' => current_user_can('manage_options')
        ];
    }
    
    /**
     * Check if user has required capability
     * @param string $capability WordPress capability
     * @return bool
     */
    protected function requireCapability($capability) {
        if (!current_user_can($capability)) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        return true;
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    protected function requireLogin() {
        if (!$this->is_logged_in) {
            wp_redirect(wp_login_url());
            exit;
        }
        return true;
    }
    
    /**
     * Get POST data safely
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getPost($key, $default = null) {
        return isset($_POST[$key]) ? sanitize_text_field($_POST[$key]) : $default;
    }
    
    /**
     * Get GET data safely
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getGet($key, $default = null) {
        return isset($_GET[$key]) ? sanitize_text_field($_GET[$key]) : $default;
    }
    
    /**
     * Redirect to URL
     * @param string $url
     */
    protected function redirect($url) {
        wp_redirect($url);
        exit;
    }
    
    /**
     * JSON response
     * @param mixed $data
     * @param int $status_code
     */
    protected function jsonResponse($data, $status_code = 200) {
        wp_send_json($data, $status_code);
    }
    
    /**
     * Error response
     * @param string $message
     * @param int $status_code
     */
    protected function errorResponse($message, $status_code = 400) {
        wp_send_json_error(['message' => $message], $status_code);
    }
    
    /**
     * Success response
     * @param mixed $data
     * @param string $message
     */
    protected function successResponse($data = null, $message = 'Success') {
        wp_send_json_success($data, $message);
    }
    
    /**
     * Get current route configuration
     * @return array|null
     */
    protected function getCurrentRouteConfig() {
        $router = HonorsApp::getInstance()->router;
        return $router->getCurrentRoute();
    }
    
    /**
     * Get current subpage configuration
     * @return array|null
     */
    protected function getCurrentSubpageConfig() {
        $route_config = $this->getCurrentRouteConfig();
        if (!$route_config) {
            return null;
        }
        
        $subpage = get_query_var('subpage');
        if ($subpage && isset($route_config['subpages'][$subpage])) {
            return $route_config['subpages'][$subpage];
        }
        
        return null;
    }
    
    /**
     * Set page title
     * @param string $title Page title
     */
    protected function setPageTitle($title) {
        $this->page_title = $title;
    }
    
    /**
     * Output page title in wp_head
     */
    public function outputPageTitle() {
        echo '<title>' . esc_html($this->page_title) . '</title>' . "\n";
    }
    
    /**
     * Setup page with route configuration
     * @param array $route_config
     */
    protected function setupPage($route_config) {
        if (!$route_config) {
            return;
        }
        
        // Set layout
        if (isset($route_config['layout'])) {
            $this->view->layout($route_config['layout']);
        }
    }
}
