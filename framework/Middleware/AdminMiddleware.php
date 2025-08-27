<?php
/**
 * Admin Middleware
 * Handles authentication and authorization for admin routes
 */
class AdminMiddleware {
    /**
     * Handle the request
     * 
     * @param mixed $request The request object/data
     * @param callable $next The next middleware in the chain
     * @return mixed
     */
    public function handle($request, callable $next) {
        // Check if user is logged in and has admin capabilities
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                status_header(403);
                wp_send_json_error([
                    'message' => 'Admin access required',
                    'code' => 'admin_required'
                ]);
                exit;
            }
            
            wp_redirect(home_url('/admin'));
            exit;
        }

        // User is authenticated and authorized, proceed to next middleware
        return $next($request);
    }
}