<?php

namespace App\Ajax;

use App\Services\VoucherService;

class SearchAjaxHandler {
    
    public function __construct() {
        add_action('wp_ajax_search_data', [$this, 'searchData']);
        add_action('wp_ajax_nopriv_search_data', [$this, 'searchData']);
    }
    
    /**
     * Handle search AJAX request
     */
    public function searchData() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'update_section_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $searchType = sanitize_text_field($_POST['search_type'] ?? '');
        $searchTerm = sanitize_text_field($_POST['search_term'] ?? '');
        
        if (empty($searchType) || empty($searchTerm)) {
            wp_send_json_success(['results' => []]);
            return;
        }
        
        try {
            $results = [];
            
            // Parse search type and filter type
            $searchParts = explode(':', $searchType);
            $baseSearchType = $searchParts[0];
            $filterType = $searchParts[1] ?? null;
            
            // Handle different search types
            switch ($baseSearchType) {
                case 'search_name_voucher':
                    $voucherService = new VoucherService();
                    $results = $voucherService->searchVouchersByName($searchTerm, 10, $filterType);
                    break;
                    
                // Add more search types here as needed
                // case 'search_name_product':
                //     $productService = new ProductService();
                //     $results = $productService->searchProductsByName($searchTerm, $filterType);
                //     break;
                    
                default:
                    wp_send_json_error(['message' => 'Invalid search type']);
                    return;
            }
            
            wp_send_json_success([
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
}
