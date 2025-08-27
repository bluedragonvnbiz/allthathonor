<?php
/**
 * URL Rewrite Handler
 * Manages custom URL rewriting rules for the theme
 */
class HonorsUrlRewrite {
    private $routes;
    private static $rewrite_flushed = false;
    
    public function __construct() {
        // Register query vars first
        add_filter('query_vars', [$this, 'registerQueryVars'], 1);
        
        // Add rewrite rules after WordPress init
        add_action('init', [$this, 'addRewriteRules'], 1);
        
        // Force our rewrite rules to be FIRST using rewrite_rules_array filter
        add_filter('rewrite_rules_array', [$this, 'prioritizeCustomRules'], 1);
        
        // Force flush rewrite rules on theme activation
        add_action('after_switch_theme', [$this, 'flushRules']);
        
        // Also flush on plugin activation/deactivation
        add_action('activated_plugin', [$this, 'flushRules']);
        add_action('deactivated_plugin', [$this, 'flushRules']);
    }
    
    /**
     * Register custom query vars
     */
    public function registerQueryVars($vars) {
        error_log('Registering query vars...');
        $vars[] = 'honors_route';
        error_log('Query vars after registration: ' . print_r($vars, true));
        return $vars;
    }
    
    /**
     * Add custom rewrite rules
     */
    public function addRewriteRules() {
        // Get routes from Router
        $routes = Router::getRoutes();
        
        if (empty($routes)) {
            error_log('No routes to add rewrite rules for');
            return;
        }
        
        // Auto-generate rewrite rules from config
        foreach ($routes as $route => $config) {
            // Add exact match rule
            $pattern = "^{$route}/?$";
            $replacement = "index.php?honors_route={$route}";
            add_rewrite_rule($pattern, $replacement, 'top');
        }
        
        // Only flush if not already flushed
        if (!self::$rewrite_flushed) {
            flush_rewrite_rules(false);
            self::$rewrite_flushed = true;
        }
    }
    
    /**
     * Force our custom rules to be FIRST in the rewrite rules array
     */
    public function prioritizeCustomRules($rules) {
        $custom_rules = [];
        $routes = Router::getRoutes();
        
        if ($routes) {
            foreach ($routes as $route => $config) {
                // Add exact match rule
                $pattern = "^{$route}/?$";
                $replacement = "index.php?honors_route={$route}";
                $custom_rules[$pattern] = $replacement;
            }
        }
        
        // Merge custom rules FIRST, then existing rules
        $merged_rules = array_merge($custom_rules, $rules);
        return $merged_rules;
    }
    
    /**
     * Flush rewrite rules
     */
    public function flushRules() {
        flush_rewrite_rules(true);
    }
}