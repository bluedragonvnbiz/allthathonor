<?php
/**
 * Admin Dashboard Controller
 * Handle admin dashboard redirect
 */

namespace Admin;

use BaseController;

class DashboardController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        // Redirect to admin/section
        wp_redirect('/admin/section/');
        exit;
    }
}