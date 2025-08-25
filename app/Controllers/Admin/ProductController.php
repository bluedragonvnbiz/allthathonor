<?php
/**
 * Product Controller
 * Handle product management page logic
 */
namespace Admin;
use App\Services\ProductService;
use App\Helpers\FieldRenderer;
use BaseController;

class ProductController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->setPageTitle('Product Management');
        
        // Get search parameters
        $searchType = $this->getGet('search_type') ?: 'product_name';
        $searchKeyword = $this->getGet('search_keyword') ?: '';
        
        // Handle status filter as array
        $statusFilter = [];
        if (isset($_GET['status']) && is_array($_GET['status'])) {
            $statusFilter = array_map('sanitize_text_field', $_GET['status']);
        } elseif (isset($_GET['status'])) {
            $statusFilter = [sanitize_text_field($_GET['status'])];
        }
        
        $page = (int)($this->getGet('current_page') ?: 1);
        $perPage = 2;
        
        // Load products with search and pagination
        $productService = new ProductService();
        $products = $productService->getAllProducts($searchType, $searchKeyword, $statusFilter, $page, $perPage);
        $totalProducts = $productService->getTotalProducts($searchType, $searchKeyword, $statusFilter);
        
        // Calculate pagination
        $totalPages = ceil($totalProducts / $perPage);
        $startRecord = ($page - 1) * $perPage + 1;
        $endRecord = min($page * $perPage, $totalProducts);
        
        // Register CSS files for product management page
        $this->view->addCSS([
            'pages/management'
        ]);
        
        // No JS needed for product list page - only for forms
        
        // Render product list view
        $this->view->render('admin/product/index', [
            'user_info' => $this->user_info,
            'products' => $products,
            'searchType' => $searchType,
            'searchKeyword' => $searchKeyword,
            'statusFilter' => $statusFilter,
            'page' => $page,
            'perPage' => $perPage,
            'totalProducts' => $totalProducts,
            'totalPages' => $totalPages,
            'startRecord' => $startRecord,
            'endRecord' => $endRecord
        ]);
    }

    public function add() {
        $this->setPageTitle('Add Product');
        
        // Load product field configuration
        $productFieldsConfig = require THEME_PATH . '/config/product_fields.php';
        
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
            'product-form'
        ]);

        // Create FieldRenderer instance
        $renderer = new FieldRenderer();
        $formHtml = $renderer->renderProductSection($productFieldsConfig, [], 'create');

        $this->view->render('admin/product/add', [
            'sectionName' => $productFieldsConfig['product_content']['title'],
            'formHtml' => $formHtml,
            'product' => [] // Empty array for new product
        ]);
    }

    public function view($productId = null) {
        $this->setPageTitle('View Product');
        
        $productId = $this->getGet('id') ?: $productId;
        
        if (!$productId) {
            wp_redirect('/admin/product/');
            exit;
        }
        
        // Load product data
        $productService = new ProductService();
        $product = $productService->getProduct($productId);
        
        if (!$product) {
            wp_redirect('/admin/product/');
            exit;
        }
        
        // Register CSS files
        $this->view->addCSS([
            'pages/management'
        ]);

        $productFieldsConfig = require THEME_PATH . '/config/product_fields.php';
        $renderer = new FieldRenderer();
        $viewHtml = $renderer->renderProductView($productFieldsConfig, $product);
        
        $this->view->render('admin/product/view', [
            'sectionName' => '상품 관리 > 상품 상세',
            'viewHtml' => $viewHtml,
            'product' => $product
        ]);
    }

    public function edit($productId = null) {
        $this->setPageTitle('Edit Product');
        
        $productId = $this->getGet('id') ?: $productId;
        
        if (!$productId) {
            wp_redirect('/admin/product/');
            exit;
        }
        
        $productService = new ProductService();
        $product = $productService->getProduct((int) $productId);
        
        if (!$product) {
            wp_redirect('/admin/product/');
            exit;
        }

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
            'product-form'
        ]);

        // Load product field configuration
        $productFieldsConfig = require THEME_PATH . '/config/product_fields.php';

        $renderer = new FieldRenderer();
 
        $formHtml = $renderer->renderProductSection($productFieldsConfig, $product, 'update');
        
        $this->view->render('admin/product/edit', [
            'sectionName' => '상품 관리 > 상품 상세',
            'formHtml' => $formHtml,
            'product' => $product
        ]);
    }

}
