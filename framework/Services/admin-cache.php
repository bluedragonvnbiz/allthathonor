<?php
/**
 * Admin Cache Management Class
 * Handle cache management in WordPress admin
 */
class HonorsAdminCache {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'handleCacheActions']);
        add_action('admin_notices', [$this, 'showAdminNotices']);
    }
    
    /**
     * Add admin menu
     */
    public function addAdminMenu() {
        add_submenu_page(
            'tools.php', // Parent menu
            'Theme Cache', // Page title
            'Theme Cache', // Menu title
            'manage_options', // Capability
            'honors-cache', // Menu slug
            [$this, 'adminPage'] // Callback function
        );
    }
    
    /**
     * Handle cache actions
     */
    public function handleCacheActions() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'honors-cache') {
            return;
        }
        
        // Check nonce for security
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'honors_cache_action')) {
            return;
        }
        
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'clear_cache':
                $this->clearCache();
                wp_redirect(admin_url('tools.php?page=honors-cache&cache_cleared=1'));
                exit;
                
            case 'enable_cache':
                $this->enableCache();
                wp_redirect(admin_url('tools.php?page=honors-cache&cache_enabled=1'));
                exit;
                
            case 'disable_cache':
                $this->disableCache();
                wp_redirect(admin_url('tools.php?page=honors-cache&cache_disabled=1'));
                exit;
        }
    }
    
    /**
     * Show admin notices
     */
    public function showAdminNotices() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'honors-cache') {
            return;
        }
        
        if (isset($_GET['cache_cleared'])) {
            echo '<div class="notice notice-success is-dismissible"><p>Cache cleared successfully!</p></div>';
        }
        
        if (isset($_GET['cache_enabled'])) {
            echo '<div class="notice notice-success is-dismissible"><p>Cache enabled successfully!</p></div>';
        }
        
        if (isset($_GET['cache_disabled'])) {
            echo '<div class="notice notice-warning is-dismissible"><p>Cache disabled successfully!</p></div>';
        }
    }
    
    /**
     * Admin page content
     */
    public function adminPage() {
        $autoloader = HonorsAutoloader::getInstance();
        $cache_enabled = $autoloader->isCacheEnabled();
        $class_map = $autoloader->getClassMap();
        $controllers = $autoloader->getControllers();
        $cache_file = get_template_directory() . '/cache/class-map.php';
        $cache_exists = file_exists($cache_file);
        
        ?>
        <div class="wrap">
            <h1>Theme Cache Management</h1>
            
            <div class="card">
                <h2>Cache Status</h2>
                <p>
                    <strong>Cache Enabled:</strong> 
                    <span style="color: <?php echo $cache_enabled ? 'green' : 'red'; ?>">
                        <?php echo $cache_enabled ? 'Yes' : 'No'; ?>
                    </span>
                </p>
                <p>
                    <strong>Cache File:</strong> 
                    <?php echo $cache_exists ? 'Exists' : 'Not found'; ?>
                </p>
                <p>
                    <strong>Total Classes:</strong> <?php echo count($class_map); ?>
                </p>
                <p>
                    <strong>Controllers:</strong> <?php echo count($controllers); ?>
                </p>
            </div>
            
            <div class="card">
                <h2>Cache Actions</h2>
                
                <p>
                    <a href="<?php echo wp_nonce_url(admin_url('tools.php?page=honors-cache&action=clear_cache'), 'honors_cache_action'); ?>" 
                       class="button button-primary">
                        Clear Cache
                    </a>
                    
                    <?php if ($cache_enabled): ?>
                        <a href="<?php echo wp_nonce_url(admin_url('tools.php?page=honors-cache&action=disable_cache'), 'honors_cache_action'); ?>" 
                           class="button button-secondary">
                            Disable Cache
                        </a>
                    <?php else: ?>
                        <a href="<?php echo wp_nonce_url(admin_url('tools.php?page=honors-cache&action=enable_cache'), 'honors_cache_action'); ?>" 
                           class="button button-secondary">
                            Enable Cache
                        </a>
                    <?php endif; ?>
                </p>
            </div>
            
            <div class="card">
                <h2>Registered Classes</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>File Path</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($class_map as $class => $path): ?>
                            <tr>
                                <td><strong><?php echo esc_html($class); ?></strong></td>
                                <td><code><?php echo esc_html($path); ?></code></td>
                                <td>
                                    <?php
                                    if (strpos($path, '/app/Controllers/') === 0) {
                                        echo '<span style="color: blue;">Controller</span>';
                                    } elseif (strpos($path, '/core/') === 0) {
                                        echo '<span style="color: green;">Core</span>';
                                    } elseif (strpos($path, '/includes/') === 0) {
                                        echo '<span style="color: orange;">Include</span>';
                                    } else {
                                        echo '<span style="color: gray;">Other</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card">
                <h2>Cache Information</h2>
                <p><strong>Cache File:</strong> <code><?php echo esc_html($cache_file); ?></code></p>
                <p><strong>Cache Size:</strong> <?php echo $cache_exists ? size_format(filesize($cache_file)) : 'N/A'; ?></p>
                <p><strong>Last Modified:</strong> <?php echo $cache_exists ? date('Y-m-d H:i:s', filemtime($cache_file)) : 'N/A'; ?></p>
            </div>
        </div>
        
        <style>
        .card {
            background: white;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
        .card h2 {
            margin-top: 0;
            color: #23282d;
        }
        </style>
        <?php
    }
    
    /**
     * Clear cache
     */
    private function clearCache() {
        $autoloader = HonorsAutoloader::getInstance();
        $autoloader->clearCache();
    }
    
    /**
     * Enable cache
     */
    private function enableCache() {
        $autoloader = HonorsAutoloader::getInstance();
        $autoloader->setCacheEnabled(true);
    }
    
    /**
     * Disable cache
     */
    private function disableCache() {
        $autoloader = HonorsAutoloader::getInstance();
        $autoloader->setCacheEnabled(false);
    }
}
