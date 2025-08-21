<?php
/**
 * Voucher Controller
 * Handle voucher management page logic
 */
use App\Services\VoucherService;
use App\Helpers\FieldRenderer;

class VoucherController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        
        // Get search parameters
        $searchType = $this->getGet('search_type') ?: 'voucher_name';
        $searchKeyword = $this->getGet('search_keyword') ?: '';
        
        // Handle status filter as array
        $statusFilter = [];
        if (isset($_GET['status']) && is_array($_GET['status'])) {
            $statusFilter = array_map('sanitize_text_field', $_GET['status']);
        } elseif (isset($_GET['status'])) {
            $statusFilter = [sanitize_text_field($_GET['status'])];
        }
        
        // Handle grade filter as array
        $gradeFilter = [];
        if (isset($_GET['grade']) && is_array($_GET['grade'])) {
            $gradeFilter = array_map('sanitize_text_field', $_GET['grade']);
        } elseif (isset($_GET['grade'])) {
            $gradeFilter = [sanitize_text_field($_GET['grade'])];
        }
        
        // Handle type filter as array
        $typeFilter = [];
        if (isset($_GET['type']) && is_array($_GET['type'])) {
            $typeFilter = array_map('sanitize_text_field', $_GET['type']);
        } elseif (isset($_GET['type'])) {
            $typeFilter = [sanitize_text_field($_GET['type'])];
        }
        
        $page = (int)($this->getGet('current_page') ?: 1);
        $perPage = 2;
        
        // Load vouchers with search and pagination
        $voucherService = new VoucherService();
        $vouchers = $voucherService->getAllVouchers($searchType, $searchKeyword, $statusFilter, $typeFilter, $gradeFilter, $page, $perPage);
        $totalVouchers = $voucherService->getTotalVouchers($searchType, $searchKeyword, $statusFilter, $typeFilter, $gradeFilter);
        
        // Calculate pagination
        $totalPages = ceil($totalVouchers / $perPage);
        $startRecord = ($page - 1) * $perPage + 1;
        $endRecord = min($page * $perPage, $totalVouchers);
        
        // Register CSS files for voucher management page
        $this->view->addCSS([
            'pages/management'
        ]);
        
        // Render voucher list view
        $this->view->render('voucher/index', [
            'user_info' => $this->user_info,
            'page_title' => 'Voucher Management',
            'vouchers' => $vouchers,
            'searchType' => $searchType,
            'searchKeyword' => $searchKeyword,
            'statusFilter' => $statusFilter,
            'gradeFilter' => $gradeFilter,
            'typeFilter' => $typeFilter,
            'page' => $page,
            'perPage' => $perPage,
            'totalVouchers' => $totalVouchers,
            'totalPages' => $totalPages,
            'startRecord' => $startRecord,
            'endRecord' => $endRecord
        ]);
    }

    public function add() {
        // Load voucher field configuration
        $voucherFieldsConfig = require THEME_PATH . '/config/voucher_fields.php';
        
        // Enqueue WordPress Media Library
        wp_enqueue_media();

        // Enqueue Quill.js
        wp_enqueue_script('quill', 'https://cdn.quilljs.com/1.3.6/quill.min.js', [], '1.3.6', true);
        wp_enqueue_style('quill', 'https://cdn.quilljs.com/1.3.6/quill.snow.css', [], '1.3.6');

        // Register CSS files
        $this->view->addCSS([
            'pages/management',
            'quill-custom'
        ]);

        // Register JS files
        $this->view->addJS([
            'voucher-form'
        ]);

        // Create FieldRenderer instance
        $renderer = new FieldRenderer();
        $formHtml = $renderer->renderSection($voucherFieldsConfig, [], 'voucher');

        $this->view->render('voucher/add', [
            'formHtml' => $formHtml,
            'voucher' => [] // Empty array for new voucher
        ]);
    }

    public function edit($voucherId = null) {
        $voucherId = $this->getGet('id') ?: $voucherId;
        
        if (!$voucherId) {
            wp_redirect('/voucher/');
            exit;
        }
        
        // Load voucher data
        $voucherService = new VoucherService();
        $voucher = $voucherService->getVoucher($voucherId);

        if (!$voucher) {
            wp_redirect('/voucher/');
            exit;
        }
        
        // Load voucher field configuration
        $voucherFieldsConfig = require THEME_PATH . '/config/voucher_fields.php';
        
        // Enqueue WordPress Media Library
        wp_enqueue_media();

        // Enqueue Quill.js
        wp_enqueue_script('quill', 'https://cdn.quilljs.com/1.3.6/quill.min.js', [], '1.3.6', true);
        wp_enqueue_style('quill', 'https://cdn.quilljs.com/1.3.6/quill.snow.css', [], '1.3.6');

        // Register CSS files
        $this->view->addCSS([
            'pages/management',
            'quill-custom'
        ]);

        // Register JS files
        $this->view->addJS([
            'voucher-form'
        ]);

        // Create FieldRenderer instance
        $renderer = new FieldRenderer();
        $formHtml = $renderer->renderSection($voucherFieldsConfig, $voucher, 'voucher');

        $this->view->render('voucher/edit', [
            'user_info' => $this->user_info,
            'page_title' => 'Edit Voucher',
            'voucher' => $voucher,
            'formHtml' => $formHtml
        ]);
    }
} 