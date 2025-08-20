<?php
/**
 * URL Rewrite Handler
 * Manages custom URL rewriting rules for the theme
 */
class HonorsUrlRewrite {
    private $routes;
    private static $rewrite_flushed = false;
    
    public function __construct() {
        // Add rewrite rules after WordPress init
        add_action('init', [$this, 'addRewriteRules'], 10);
        
        // Force our rewrite rules to be FIRST using rewrite_rules_array filter
        add_filter('rewrite_rules_array', [$this, 'prioritizeCustomRules'], 1);
    }
    
    /**
     * Add custom rewrite rules
     */
    public function addRewriteRules() {
        // Get routes from Router
        $routes = Router::getRoutes();
        
        if (empty($routes)) {
            return;
        }
        
        // Auto-generate rewrite rules from config
        foreach ($routes as $route => $config) {
            // Handle subpage routes (e.g., management/abc)
            if (strpos($route, '/') !== false) {
                $parts = explode('/', $route);
                $parent_page = $parts[0];
                $subpage = $parts[1];
                
                $pattern = "^{$parent_page}/{$subpage}/?$";
                $replacement = "index.php?pagename={$parent_page}&subpage={$subpage}";
                
                add_rewrite_rule($pattern, $replacement, 'top');
            }
            // Also add a generic pattern for the parent page to catch dynamic subpages
            else {
                $pattern = "^{$route}/([^/]+)/?$";
                $replacement = "index.php?pagename={$route}&subpage=\$matches[1]";
                
                add_rewrite_rule($pattern, $replacement, 'top');
            }
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
        // Extract our custom rules (avoid duplicates)
        $custom_rules = [];
        $routes = Router::getRoutes();
        $added_patterns = []; // Track added patterns to avoid duplicates
        
        if ($routes) {
            foreach ($routes as $route => $config) {
                if (strpos($route, '/') !== false) {
                    $parts = explode('/', $route);
                    $parent_page = $parts[0];
                    $subpage = $parts[1];
                    
                    // Add specific rule for this exact subpage
                    $specific_pattern = "^{$parent_page}/{$subpage}/?$";
                    $specific_replacement = "index.php?pagename={$parent_page}&subpage={$subpage}";
                    $custom_rules[$specific_pattern] = $specific_replacement;
                    
                    // Add generic pattern only once per parent page
                    $generic_pattern = "^{$parent_page}/([^/]+)/?$";
                    if (!in_array($generic_pattern, $added_patterns)) {
                        $generic_replacement = "index.php?pagename={$parent_page}&subpage=\$matches[1]";
                        $custom_rules[$generic_pattern] = $generic_replacement;
                        $added_patterns[] = $generic_pattern;
                    }
                }
            }
        }
        
        // Merge custom rules FIRST, then existing rules
        return array_merge($custom_rules, $rules);
    }


    
    /**
     * Build URL for subpage
     * @param string $parent_page
     * @param string $subpage
     * @return string
     */
    public static function buildSubpageUrl($parent_page, $subpage) {
        return home_url("/{$parent_page}/{$subpage}/");
    }
}
