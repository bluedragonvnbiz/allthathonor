<?php
/**
 * Live Chat Controller
 * Handle live chat page logic
 */
namespace Admin;
use BaseController;

class LiveChatController extends BaseController {
    public function __construct() {
        parent::__construct();
    }
    public function index() {
        // Set page title
        $this->setPageTitle('Live Chat Management');

        // Enqueue WordPress Media Library
        wp_enqueue_media();

        // Register CSS files for live chat management page
        $this->view->addCSS([
            'pages/management'
        ]);

        // Render live chat management page
        $this->view->render('admin/live-chat/index');
    }
}