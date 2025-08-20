<?php

namespace App\Ajax;

use App\Services\ProductService;

class ProductAjaxHandler {
    
    public function __construct() {
        add_action('wp_ajax_create_product', [$this, 'createProduct']);
        add_action('wp_ajax_nopriv_create_product', [$this, 'createProduct']);
        add_action('wp_ajax_update_product', [$this, 'updateProduct']);
        add_action('wp_ajax_nopriv_update_product', [$this, 'updateProduct']);
        add_action('wp_ajax_delete_product', [$this, 'deleteProduct']);
        add_action('wp_ajax_nopriv_delete_product', [$this, 'deleteProduct']);
        add_action('wp_ajax_get_product', [$this, 'getProduct']);
        add_action('wp_ajax_nopriv_get_product', [$this, 'getProduct']);
        add_action('wp_ajax_get_attachment_id_by_url', [$this, 'getAttachmentIdByUrl']);
        add_action('wp_ajax_nopriv_get_attachment_id_by_url', [$this, 'getAttachmentIdByUrl']);
    }
    
    /**
     * Handle product creation AJAX request
     */
    public function createProduct() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'product_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        try {
            $productService = new ProductService();
            
            // Sanitize data
            $data = $this->sanitizeProductData($_POST);
            
            // Create product
            $productId = $productService->createProduct($data);
            
            wp_send_json_success([
                'message' => 'Product created successfully',
                'product_id' => $productId
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle product update AJAX request
     */
    public function updateProduct() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'product_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $productId = (int) $_POST['id'];
        
        if (!$productId) {
            wp_send_json_error(['message' => 'Product ID is required']);
            return;
        }
        
        try {
            $productService = new ProductService();
            
            // Sanitize data
            $data = $this->sanitizeProductData($_POST);
            
            // Update product
            $productService->updateProduct($productId, $data);
            
            wp_send_json_success([
                'message' => 'Product updated successfully',
                'product_id' => $productId
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle product deletion AJAX request
     */
    public function deleteProduct() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'product_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $productId = (int) $_POST['product_id'];
        
        if (!$productId) {
            wp_send_json_error(['message' => 'Product ID is required']);
            return;
        }
        
        try {
            $productService = new ProductService();
            
            // Delete product
            $productService->deleteProduct($productId);
            
            wp_send_json_success([
                'message' => 'Product deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle get product AJAX request
     */
    public function getProduct() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'product_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $productId = (int) $_POST['product_id'];
        
        if (!$productId) {
            wp_send_json_error(['message' => 'Product ID is required']);
            return;
        }
        
        try {
            $productService = new ProductService();
            
            // Get product
            $product = $productService->getProduct($productId);
            
            if (!$product) {
                wp_send_json_error(['message' => 'Product not found']);
                return;
            }
            
            wp_send_json_success([
                'product' => $product
            ]);
            
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle get attachment ID by URL AJAX request
     */
    public function getAttachmentIdByUrl() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'product_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        $url = sanitize_url($_POST['url']);
        
        if (!$url) {
            wp_send_json_error(['message' => 'URL is required']);
            return;
        }
        
        global $wpdb;
        
        $attachment_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM $wpdb->posts WHERE guid = %s AND post_type = 'attachment'",
            $url
        ));
        
        if ($attachment_id) {
            wp_send_json_success([
                'attachment_id' => (int) $attachment_id
            ]);
        } else {
            wp_send_json_error([
                'message' => 'Attachment not found'
            ]);
        }
    }
    
    /**
     * Sanitize product data
     */
    private function sanitizeProductData($data) {
        return [
            'exposure_status' => sanitize_text_field($data['exposure_status'] ?? 'expose'),
            'main_image' => sanitize_url($data['main_image'] ?? ''),
            'product_name' => sanitize_text_field($data['product_name'] ?? ''),
            'product_name_en' => sanitize_text_field($data['product_name_en'] ?? ''),
            'summary_description' => sanitize_textarea_field($data['summary_description'] ?? ''),
            'detailed_description' => wp_kses_post($data['detailed_description'] ?? '')
        ];
    }
}

// Initialize AJAX handler
new ProductAjaxHandler();
