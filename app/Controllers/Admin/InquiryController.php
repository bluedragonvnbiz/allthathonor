<?php
/**
 * Inquiry Controller
 * Handle inquiry page logic
 */
namespace Admin;
use App\Services\InquiryService;
use BaseController;

class InquiryController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    public function index() {
        $this->setPageTitle('Inquiry Management');
        
        // Get search parameters
        $searchType = $this->getGet('search_type') ?: 'corporate_name';
        $searchKeyword = $this->getGet('search_keyword') ?: '';
        $categoryMain = $this->getGet('category_main') ?: '';
        $categorySub = $this->getGet('category_sub') ?: '';
        
        // Handle status filter as array
        $statusFilter = [];
        if (isset($_GET['status']) && is_array($_GET['status'])) {
            $statusFilter = array_map('sanitize_text_field', $_GET['status']);
        } elseif (isset($_GET['status'])) {
            $statusFilter = [sanitize_text_field($_GET['status'])];
        }
        
        $page = (int)($this->getGet('current_page') ?: 1);
        $perPage = 3;
        
        // Load inquiries with search and pagination
        $inquiryService = new InquiryService();
        $inquiryData = $inquiryService->getInquiries([
            'search_type' => $searchType,
            'search_keyword' => $searchKeyword,
            'category_main' => $categoryMain,
            'category_sub' => $categorySub,
            'status' => $statusFilter,
            'current_page' => $page,
            'per_page' => $perPage
        ]);
        
        // Get categories for filter dropdowns
        $categories = $inquiryService->getCategories();
        
        // Format inquiries for display
        $formattedInquiries = [];
        foreach ($inquiryData['inquiries'] as $inquiry) {
            $formattedInquiries[] = $inquiryService->formatInquiry($inquiry);
        }
        
        // Calculate pagination
        $totalInquiries = $inquiryData['total_inquiries'];
        $totalPages = $inquiryData['total_pages'];
        $startRecord = ($page - 1) * $perPage + 1;
        $endRecord = min($page * $perPage, $totalInquiries);
        
        // Register CSS files for inquiry management page
        $this->view->addCSS([
            'pages/management'
        ]);
        
        // Register JS files for dynamic category loading
        $this->view->addJS([
            'inquiry-management'
        ]);
        
        // Render inquiry list view
        $this->view->render('admin/inquiry/index', [
            'user_info' => $this->user_info,
            'inquiries' => $formattedInquiries,
            'searchType' => $searchType,
            'searchKeyword' => $searchKeyword,
            'categoryMain' => $categoryMain,
            'categorySub' => $categorySub,
            'statusFilter' => $statusFilter,
            'categories' => $categories['categories'],
            'mainCategories' => $categories['main_categories'],
            'subCategories' => $categories['sub_categories'],
            'page' => $page,
            'perPage' => $perPage,
            'totalInquiries' => $totalInquiries,
            'totalPages' => $totalPages,
            'startRecord' => $startRecord,
            'endRecord' => $endRecord
        ]);
    }

    public function view() {
        $this->setPageTitle('View Inquiry');
        
        $inquiryService = new InquiryService();
        $inquiry = $inquiryService->getInquiryById($this->getGet('id'));

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
            'inquiry-management'
        ]);

        $this->view->render('admin/inquiry/view', [
            'inquiry' => $inquiry
        ]);
    }
} 