<?php

namespace App\Services;

use App\Database\ProductDatabase;

class ProductService {
    private const CACHE_PREFIX = 'product:';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get product by ID
     */
    public function getProduct(int $productId) {
        // Try to get from cache first
        $cached = wp_cache_get($this->getCacheKey($productId));
        
        if ($cached !== false) {
            return json_decode($cached, true);
        }

        global $wpdb;
        $table_name = ProductDatabase::getTableName();
        
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $productId
            )
        );

        if (!$result) {
            return null;
        }

        $product = (array) $result;
        
        // Store in cache
        wp_cache_set($this->getCacheKey($productId), json_encode($product), '', self::CACHE_TTL);

        return $product;
    }

    /**
     * Get all products with search and filter
     */
    public function getAllProducts(string $searchType = 'product_name', string $searchKeyword = '', $statusFilter = [], int $page = 1, int $perPage = 10): array {
        global $wpdb;
        $table_name = ProductDatabase::getTableName();
        
        $where_conditions = [];
        $where_values = [];
        
        // Search by keyword
        if (!empty($searchKeyword)) {
            $where_conditions[] = "$searchType LIKE %s";
            $where_values[] = '%' . $wpdb->esc_like($searchKeyword) . '%';
        }
        
        // Filter by status - handle both array and string
        if (!empty($statusFilter)) {
            if (is_array($statusFilter)) {
                $status_placeholders = array_fill(0, count($statusFilter), '%s');
                $where_conditions[] = "exposure_status IN (" . implode(',', $status_placeholders) . ")";
                $where_values = array_merge($where_values, $statusFilter);
            } else {
                $where_conditions[] = "exposure_status = %s";
                $where_values[] = $statusFilter;
            }
        }
        
        // Build query
        $query = "SELECT * FROM $table_name";
        if (!empty($where_conditions)) {
            $query .= " WHERE " . implode(' AND ', $where_conditions);
        }
        $query .= " ORDER BY created_at DESC";
        
        // Add pagination
        $offset = ($page - 1) * $perPage;
        $query .= " LIMIT $perPage OFFSET $offset";
        
        // Prepare and execute query
        if (!empty($where_values)) {
            $results = $wpdb->get_results($wpdb->prepare($query, $where_values));
        } else {
            $results = $wpdb->get_results($query);
        }

        return array_map(function($result) {
            return (array) $result;
        }, $results);
    }
    
    /**
     * Get total count for pagination
     */
    public function getTotalProducts(string $searchType = 'product_name', string $searchKeyword = '', $statusFilter = []): int {
        global $wpdb;
        $table_name = ProductDatabase::getTableName();
        
        $where_conditions = [];
        $where_values = [];
        
        // Search by keyword
        if (!empty($searchKeyword)) {
            $where_conditions[] = "$searchType LIKE %s";
            $where_values[] = '%' . $wpdb->esc_like($searchKeyword) . '%';
        }
        
        // Filter by status - handle both array and string
        if (!empty($statusFilter)) {
            if (is_array($statusFilter)) {
                $status_placeholders = array_fill(0, count($statusFilter), '%s');
                $where_conditions[] = "exposure_status IN (" . implode(',', $status_placeholders) . ")";
                $where_values = array_merge($where_values, $statusFilter);
            } else {
                $where_conditions[] = "exposure_status = %s";
                $where_values[] = $statusFilter;
            }
        }
        
        // Build count query
        $query = "SELECT COUNT(*) FROM $table_name";
        if (!empty($where_conditions)) {
            $query .= " WHERE " . implode(' AND ', $where_conditions);
        }
        
        // Execute count query
        if (!empty($where_values)) {
            return (int) $wpdb->get_var($wpdb->prepare($query, $where_values));
        } else {
            return (int) $wpdb->get_var($query);
        }
    }

    /**
     * Get product options for select dropdowns
     */
    public function getProductOptions(): array {
        $cacheKey = 'product_options_select';
        $options = wp_cache_get($cacheKey);
        
        if (false === $options) {
            global $wpdb;
            $table_name = ProductDatabase::getTableName();
            
            try {
                $products = $wpdb->get_results(
                    "SELECT id, product_name 
                     FROM {$table_name} 
                     WHERE exposure_status = 'expose' 
                     ORDER BY id ASC",
                    ARRAY_A
                );
                
                $options = [];
                foreach ($products as $product) {
                    // Format: Product Name (ID)
                    $displayName = sprintf(
                        '%s (PT%s)',
                        $product['product_name'],
                        str_pad($product['id'], 6, '0', STR_PAD_LEFT)
                    );
                    
                    // Use ID as key
                    $options[$product['id']] = $displayName;
                }
                
                // Cache for 1 hour
                wp_cache_set($cacheKey, $options, '', 3600);
                
            } catch (\Exception $e) {
                error_log("Error loading product options: " . $e->getMessage());
                $options = [
                    1 => '고객맞춤서비스 (1)',
                    2 => '고객맞춤서비스 (2)',
                    3 => '고객맞춤서비스 (3)'
                ];
            }
        }
        
        return $options;
    }

    /**
     * Create new product
     */
    public function createProduct(array $data): int {
        $this->validateProductData($data);

        global $wpdb;
        $table_name = ProductDatabase::getTableName();

        $result = $wpdb->insert(
            $table_name,
            [
                'exposure_status' => $data['exposure_status'] ?? 'expose',
                'main_image' => $data['main_image'] ?? '',
                'product_name' => $data['product_name'],
                'product_name_en' => $data['product_name_en'] ?? '',
                'summary_description' => $data['summary_description'] ?? '',
                'detailed_description' => $data['detailed_description'] ?? ''
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s']
        );

        if ($result === false) {
            throw new \Exception("Failed to create product");
        }

        $productId = $wpdb->insert_id;
        
        // Clear cache
        wp_cache_delete($this->getCacheKey($productId));

        return $productId;
    }

    /**
     * Update product
     */
    public function updateProduct(int $productId, array $data): bool {
        $this->validateProductData($data);

        global $wpdb;
        $table_name = ProductDatabase::getTableName();

        $result = $wpdb->update(
            $table_name,
            [
                'exposure_status' => $data['exposure_status'] ?? 'expose',
                'main_image' => $data['main_image'] ?? '',
                'product_name' => $data['product_name'],
                'product_name_en' => $data['product_name_en'] ?? '',
                'summary_description' => $data['summary_description'] ?? '',
                'detailed_description' => $data['detailed_description'] ?? ''
            ],
            ['id' => $productId],
            ['%s', '%s', '%s', '%s', '%s', '%s'],
            ['%d']
        );

        if ($result === false) {
            throw new \Exception("Failed to update product: {$productId}");
        }

        // Clear cache
        wp_cache_delete($this->getCacheKey($productId));

        return true;
    }

    /**
     * Delete product
     */
    public function deleteProduct(int $productId): bool {
        global $wpdb;
        $table_name = ProductDatabase::getTableName();

        $result = $wpdb->delete(
            $table_name,
            ['id' => $productId],
            ['%d']
        );

        if ($result === false) {
            throw new \Exception("Failed to delete product: {$productId}");
        }

        // Clear cache
        wp_cache_delete($this->getCacheKey($productId));

        return true;
    }

    /**
     * Validate product data
     */
    private function validateProductData(array $data) {
        $requiredFields = [
            'product_name' => '상품명',
            'product_name_en' => '상품명(영문)',
            'summary_description' => '요약 설명',
            'detailed_description' => '상세 설명',
            'main_image' => '메인 이미지',
            'exposure_status' => '노출상태'
        ];

        $errors = [];

        foreach ($requiredFields as $field => $label) {
            if (empty($data[$field])) {
                $errors[] = "{$label}은(는) 필수 입력 항목입니다.";
            }
        }

        // Validate exposure status if provided
        if (isset($data['exposure_status']) && !empty($data['exposure_status']) && 
            !in_array($data['exposure_status'], ['expose', 'not_expose'])) {
            $errors[] = "노출상태가 올바르지 않습니다.";
        }

        if (!empty($errors)) {
            throw new \Exception(implode("\n", $errors));
        }
    }

    /**
     * Get cache key for product
     */
    private function getCacheKey(int $productId): string {
        return self::CACHE_PREFIX . $productId;
    }
}
