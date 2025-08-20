<?php
/**
 * Membership Controller
 * Handle membership page logic
 */
class MembershipController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        // Set page title
        $this->setPageTitle('All That Honors Club - Membership');
        
        // Get data for inquiry page
        $data = [
        ];

        // Register CSS files for home page
        $this->view->addCSS([
            'global-css'
        ]);

        // Register JS files for home page
        $this->view->addJS([
            'home'
        ]);
        
        // Render home page
        $this->view->render('pages/membership', $data);
    }
} 