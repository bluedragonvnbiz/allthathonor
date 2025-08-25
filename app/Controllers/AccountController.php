<?php
/**
 * Account Controller
 * Handle account-related pages: login, register, forgot password, etc.
 */
class AccountController {
    private $view;
    
    public function __construct() {
        $this->view = HonorsApp::getInstance()->view;
    }
    
    /**
     * Login page
     */
    public function login() {
        // Register CSS files for login page
        $this->view->addCSS([
            'pages/login'
        ]);
        
        // Register JS files for AJAX login
        $this->view->addJS([
            'login-ajax'
        ]);
        
        // Set login layout
        $this->view->layout('login');
        
        // Render login view
        $this->view->render('pages/login', [
            'page_title' => 'Login - All That Honors Club',
            'form_action' => wp_login_url(),
            'redirect_to' => isset($_GET['redirect_to']) ? $_GET['redirect_to'] : home_url('/admin/section')
        ]);
    }
    
    /**
     * Register page
     */
    public function register() {
        // TODO: Implement register page
        $this->view->addCSS([
            'pages/register'
        ]);
        
        $this->view->layout('login'); // Use same layout as login
        
        $this->view->render('register', [
            'page_title' => 'Register - All That Honors Club'
        ]);
    }
    
    /**
     * Forgot password page
     */
    public function forgotPassword() {
        // TODO: Implement forgot password page
        $this->view->addCSS([
            'pages/forgot-password'
        ]);
        
        $this->view->layout('login'); // Use same layout as login
        
        $this->view->render('forgot-password', [
            'page_title' => 'Forgot Password - All That Honors Club'
        ]);
    }
    
    /**
     * Reset password page
     */
    public function resetPassword() {
        // TODO: Implement reset password page
        $this->view->addCSS([
            'pages/reset-password'
        ]);
        
        $this->view->layout('login'); // Use same layout as login
        
        $this->view->render('reset-password', [
            'page_title' => 'Reset Password - All That Honors Club'
        ]);
    }
    
    /**
     * Profile page
     */
    public function profile() {
        // TODO: Implement profile page
        $this->view->addCSS([
            'pages/profile'
        ]);
        
        $this->view->layout('main');
        
        $this->view->render('profile', [
            'page_title' => 'My Profile - All That Honors Club'
        ]);
    }
}
