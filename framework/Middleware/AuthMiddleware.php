<?php
/**
 * Auth Middleware
 * Handles user authentication for protected routes
 */
class AuthMiddleware {
    /**
     * Handle the request
     * 
     * @param mixed $request The request object/data
     * @param callable $next The next middleware in the chain
     * @return mixed
     */
    public function handle($request, callable $next) {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                status_header(401);
                wp_send_json_error([
                    'message' => 'Authentication required',
                    'code' => 'auth_required'
                ]);
                exit;
            }
            
            // Store current URL for redirect back after login
            $current_url = home_url(remove_query_arg('redirect_to', $_SERVER['REQUEST_URI']));
            $redirect_to = add_query_arg('redirect_to', urlencode($current_url), home_url('/login'));
            
            wp_redirect($redirect_to);
            exit;
        }

        // User is authenticated, proceed to next middleware
        return $next($request);
    }
}