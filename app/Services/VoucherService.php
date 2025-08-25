<?php

namespace App\Services;

use App\Database\VoucherDatabase;

class VoucherService {
    private const CACHE_PREFIX = 'voucher:';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get voucher by ID
     */
    public function getVoucher(int $voucherId) {
        // Try to get from cache first
        $cached = wp_cache_get($this->getCacheKey($voucherId));
        if ($cached !== false) {
            return $cached;
        }

        global $wpdb;
        $table_name = VoucherDatabase::getTableName();
        
        $voucher = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $voucherId
        ), ARRAY_A);
        
        if ($voucher) {
            // Cache the result
            wp_cache_set($this->getCacheKey($voucherId), $voucher, '', self::CACHE_TTL);
        }

        return $voucher;
    }

    /**
     * Get all vouchers with search and filter
     */
    public function getAllVouchers(string $searchType = 'name', string $searchKeyword = '', $statusFilter = [], $typeFilter = [], $categoryFilter = [], int $page = 1, int $perPage = 10): array {
        global $wpdb;
        $table_name = VoucherDatabase::getTableName();
        
        $where_conditions = [];
        $where_values = [];
        
        // Search by keyword
        if (!empty($searchKeyword)) {
            if ($searchType === 'voucher_code') {
                // Remove 'BF' prefix if user includes it for more flexible search
                $searchCode = str_replace('BF', '', $searchKeyword);
                // Support partial code search by padding with zeros and using LIKE
                if (is_numeric($searchCode)) {
                    // If numeric, search both as ID and formatted code
                    $where_conditions[] = "(id = %d OR LPAD(id, 6, '0') LIKE %s)";
                    $where_values[] = (int) $searchCode;
                    $where_values[] = '%' . $wpdb->esc_like(str_pad($searchCode, 6, '0', STR_PAD_LEFT)) . '%';
                } else {
                    // If not numeric, just search the keyword in formatted code
                    $where_conditions[] = "LPAD(id, 6, '0') LIKE %s";
                    $where_values[] = '%' . $wpdb->esc_like($searchKeyword) . '%';
                }
            } elseif ($searchType === 'voucher_name') {
                // Map voucher_name to actual column name 'name'
                $where_conditions[] = "name LIKE %s";
                $where_values[] = '%' . $wpdb->esc_like($searchKeyword) . '%';
            } else {
                // Fallback for other search types
                $where_conditions[] = "$searchType LIKE %s";
                $where_values[] = '%' . $wpdb->esc_like($searchKeyword) . '%';
            }
        }
        
        // Filter by status
        if (!empty($statusFilter)) {
            // If 'all' is selected, don't filter by status
            if (!in_array('all', $statusFilter)) {
                if (is_array($statusFilter)) {
                    $status_placeholders = array_fill(0, count($statusFilter), '%s');
                    $where_conditions[] = "status IN (" . implode(',', $status_placeholders) . ")";
                    $where_values = array_merge($where_values, $statusFilter);
                } else {
                    $where_conditions[] = "status = %s";
                    $where_values[] = $statusFilter;
                }
            }
        }

        // Filter by type
        if (!empty($typeFilter)) {
            // If 'all' is selected, don't filter by type
            if (!in_array('all', $typeFilter)) {
                // Check if 'unclassified' is selected
                if (in_array('unclassified', $typeFilter)) {
                    // Remove 'unclassified' from filter array
                    $typeFilter = array_filter($typeFilter, function($type) {
                        return $type !== 'unclassified';
                    });
                    
                    if (!empty($typeFilter)) {
                        // Both specific types and unclassified selected
                        $type_conditions = [];
                        foreach ($typeFilter as $type) {
                            $type_conditions[] = "type LIKE %s";
                            $where_values[] = '%' . $wpdb->esc_like($type) . '%';
                        }
                        // Add unclassified condition (empty or not matching defined types)
                        $type_conditions[] = "(type IS NULL OR type = '' OR (type NOT LIKE %s AND type NOT LIKE %s))";
                        $where_values[] = '%voucher%';
                        $where_values[] = '%event_invitation%';
                        $where_conditions[] = "(" . implode(' OR ', $type_conditions) . ")";
                    } else {
                        // Only unclassified selected
                        $where_conditions[] = "(type IS NULL OR type = '' OR (type NOT LIKE %s AND type NOT LIKE %s))";
                        $where_values[] = '%voucher%';
                        $where_values[] = '%event_invitation%';
                    }
                } else {
                    // Only specific types selected
                    if (is_array($typeFilter)) {
                        $type_conditions = [];
                        foreach ($typeFilter as $type) {
                            $type_conditions[] = "type LIKE %s";
                            $where_values[] = '%' . $wpdb->esc_like($type) . '%';
                        }
                        $where_conditions[] = "(" . implode(' OR ', $type_conditions) . ")";
                    } else {
                        $where_conditions[] = "type LIKE %s";
                        $where_values[] = '%' . $wpdb->esc_like($typeFilter) . '%';
                    }
                }
            }
        }

        // Filter by category (grade) - now using membership IDs
        if (!empty($categoryFilter)) {
            // If 'all' is selected, don't filter by category
            if (!in_array('all', $categoryFilter)) {
                // Check if 'unclassified' is selected
                if (in_array('unclassified', $categoryFilter)) {
                    // Remove 'unclassified' from filter array
                    $categoryFilter = array_filter($categoryFilter, function($category) {
                        return $category !== 'unclassified';
                    });
                    
                    if (!empty($categoryFilter)) {
                        // Both specific categories and unclassified selected
                        $category_conditions = [];
                        foreach ($categoryFilter as $categoryId) {
                            if (is_numeric($categoryId)) {
                                $category_conditions[] = "FIND_IN_SET(%s, category) > 0";
                                $where_values[] = $categoryId;
                            }
                        }
                        // Add unclassified condition (empty or no valid membership IDs)
                        $category_conditions[] = "(category IS NULL OR category = '' OR category NOT REGEXP '^[0-9,]+$')";
                        $where_conditions[] = "(" . implode(' OR ', $category_conditions) . ")";
                    } else {
                        // Only unclassified selected
                        $where_conditions[] = "(category IS NULL OR category = '' OR category NOT REGEXP '^[0-9,]+$')";
                    }
                } else {
                    // Only specific categories selected
                    if (is_array($categoryFilter)) {
                        $category_conditions = [];
                        foreach ($categoryFilter as $categoryId) {
                            if (is_numeric($categoryId)) {
                                $category_conditions[] = "FIND_IN_SET(%s, category) > 0";
                                $where_values[] = $categoryId;
                            }
                        }
                        if (!empty($category_conditions)) {
                            $where_conditions[] = "(" . implode(' OR ', $category_conditions) . ")";
                        }
                    } else {
                        if (is_numeric($categoryFilter)) {
                            $where_conditions[] = "FIND_IN_SET(%s, category) > 0";
                            $where_values[] = $categoryFilter;
                        }
                    }
                }
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

        // Debug: Log any SQL errors
        if ($wpdb->last_error) {
            error_log('VoucherService SQL Error: ' . $wpdb->last_error);
            error_log('VoucherService Query: ' . $query);
            error_log('VoucherService Values: ' . print_r($where_values, true));
        }

        // Ensure $results is an array
        if ($results === null) {
            $results = [];
        }

        return array_map(function($result) {
            return (array) $result;
        }, $results);
    }
    
    /**
     * Get total count for pagination
     */
    public function getTotalVouchers(string $searchType = 'name', string $searchKeyword = '', $statusFilter = [], $typeFilter = [], $categoryFilter = []): int {
        global $wpdb;
        $table_name = VoucherDatabase::getTableName();
        
        $where_conditions = [];
        $where_values = [];
        
        // Search by keyword
        if (!empty($searchKeyword)) {
            if ($searchType === 'voucher_code') {
                // Remove 'BF' prefix if user includes it for more flexible search
                $searchCode = str_replace('BF', '', $searchKeyword);
                // Support partial code search by padding with zeros and using LIKE
                if (is_numeric($searchCode)) {
                    // If numeric, search both as ID and formatted code
                    $where_conditions[] = "(id = %d OR LPAD(id, 6, '0') LIKE %s)";
                    $where_values[] = (int) $searchCode;
                    $where_values[] = '%' . $wpdb->esc_like(str_pad($searchCode, 6, '0', STR_PAD_LEFT)) . '%';
                } else {
                    // If not numeric, just search the keyword in formatted code
                    $where_conditions[] = "LPAD(id, 6, '0') LIKE %s";
                    $where_values[] = '%' . $wpdb->esc_like($searchKeyword) . '%';
                }
            } elseif ($searchType === 'voucher_name') {
                // Map voucher_name to actual column name 'name'
                $where_conditions[] = "name LIKE %s";
                $where_values[] = '%' . $wpdb->esc_like($searchKeyword) . '%';
            } else {
                // Fallback for other search types
                $where_conditions[] = "$searchType LIKE %s";
                $where_values[] = '%' . $wpdb->esc_like($searchKeyword) . '%';
            }
        }
        
        // Filter by status
        if (!empty($statusFilter)) {
            // If 'all' is selected, don't filter by status
            if (!in_array('all', $statusFilter)) {
                if (is_array($statusFilter)) {
                    $status_placeholders = array_fill(0, count($statusFilter), '%s');
                    $where_conditions[] = "status IN (" . implode(',', $status_placeholders) . ")";
                    $where_values = array_merge($where_values, $statusFilter);
                } else {
                    $where_conditions[] = "status = %s";
                    $where_values[] = $statusFilter;
                }
            }
        }

        // Filter by type
        if (!empty($typeFilter)) {
            // If 'all' is selected, don't filter by type
            if (!in_array('all', $typeFilter)) {
                // Check if 'unclassified' is selected
                if (in_array('unclassified', $typeFilter)) {
                    // Remove 'unclassified' from filter array
                    $typeFilter = array_filter($typeFilter, function($type) {
                        return $type !== 'unclassified';
                    });
                    
                    if (!empty($typeFilter)) {
                        // Both specific types and unclassified selected
                        $type_conditions = [];
                        foreach ($typeFilter as $type) {
                            $type_conditions[] = "type LIKE %s";
                            $where_values[] = '%' . $wpdb->esc_like($type) . '%';
                        }
                        // Add unclassified condition (empty or not matching defined types)
                        $type_conditions[] = "(type IS NULL OR type = '' OR (type NOT LIKE %s AND type NOT LIKE %s))";
                        $where_values[] = '%voucher%';
                        $where_values[] = '%event_invitation%';
                        $where_conditions[] = "(" . implode(' OR ', $type_conditions) . ")";
                    } else {
                        // Only unclassified selected
                        $where_conditions[] = "(type IS NULL OR type = '' OR (type NOT LIKE %s AND type NOT LIKE %s))";
                        $where_values[] = '%voucher%';
                        $where_values[] = '%event_invitation%';
                    }
                } else {
                    // Only specific types selected
                    if (is_array($typeFilter)) {
                        $type_conditions = [];
                        foreach ($typeFilter as $type) {
                            $type_conditions[] = "type LIKE %s";
                            $where_values[] = '%' . $wpdb->esc_like($type) . '%';
                        }
                        $where_conditions[] = "(" . implode(' OR ', $type_conditions) . ")";
                    } else {
                        $where_conditions[] = "type LIKE %s";
                        $where_values[] = '%' . $wpdb->esc_like($typeFilter) . '%';
                    }
                }
            }
        }

        // Filter by category (grade) - now using membership IDs
        if (!empty($categoryFilter)) {
            // If 'all' is selected, don't filter by category
            if (!in_array('all', $categoryFilter)) {
                // Check if 'unclassified' is selected
                if (in_array('unclassified', $categoryFilter)) {
                    // Remove 'unclassified' from filter array
                    $categoryFilter = array_filter($categoryFilter, function($category) {
                        return $category !== 'unclassified';
                    });
                    
                    if (!empty($categoryFilter)) {
                        // Both specific categories and unclassified selected
                        $category_conditions = [];
                        foreach ($categoryFilter as $categoryId) {
                            if (is_numeric($categoryId)) {
                                $category_conditions[] = "FIND_IN_SET(%s, category) > 0";
                                $where_values[] = $categoryId;
                            }
                        }
                        // Add unclassified condition (empty or no valid membership IDs)
                        $category_conditions[] = "(category IS NULL OR category = '' OR category NOT REGEXP '^[0-9,]+$')";
                        $where_conditions[] = "(" . implode(' OR ', $category_conditions) . ")";
                    } else {
                        // Only unclassified selected
                        $where_conditions[] = "(category IS NULL OR category = '' OR category NOT REGEXP '^[0-9,]+$')";
                    }
                } else {
                    // Only specific categories selected
                    if (is_array($categoryFilter)) {
                        $category_conditions = [];
                        foreach ($categoryFilter as $categoryId) {
                            if (is_numeric($categoryId)) {
                                $category_conditions[] = "FIND_IN_SET(%s, category) > 0";
                                $where_values[] = $categoryId;
                            }
                        }
                        if (!empty($category_conditions)) {
                            $where_conditions[] = "(" . implode(' OR ', $category_conditions) . ")";
                        }
                    } else {
                        if (is_numeric($categoryFilter)) {
                            $where_conditions[] = "FIND_IN_SET(%s, category) > 0";
                            $where_values[] = $categoryFilter;
                        }
                    }
                }
            }
        }
        
        // Build query
        $query = "SELECT COUNT(*) FROM $table_name";
        if (!empty($where_conditions)) {
            $query .= " WHERE " . implode(' AND ', $where_conditions);
        }
        
        // Execute query
        if (!empty($where_values)) {
            $result = $wpdb->get_var($wpdb->prepare($query, $where_values));
        } else {
            $result = $wpdb->get_var($query);
        }
        
        return (int) $result;
    }

    /**
     * Create new voucher
     */
    public function createVoucher(array $data): ?int {
        global $wpdb;

        // Validate required fields
        if (empty($data['name'])) {
            return null;
        }

        // Prepare data
        $voucherData = [
            'type' => $this->formatMultiSelect($data['type'] ?? []),
            'status' => $data['status'] ?? 'expose',
            'category' => $this->formatMultiSelect($data['category'] ?? []),
            'image' => $data['image'] ?? null,
            'name' => sanitize_text_field($data['name']),
            'short_description' => sanitize_textarea_field($data['short_description'] ?? ''),
            'detail_description' => wp_kses_post($data['detail_description'] ?? ''),
        ];

        // Get table name
        $table_name = VoucherDatabase::getTableName();
        
        // Insert voucher
        $result = $wpdb->insert(
            $table_name,
            $voucherData,
            [
                '%s', // type
                '%s', // status
                '%s', // category
                '%s', // image
                '%s', // name
                '%s', // short_description
                '%s'  // detail_description
            ]
        );
        
        if ($result !== false) {
            $voucherId = $wpdb->insert_id;
            // Clear cache
            $this->clearCache();
            return $voucherId;
        }

        return null;
    }

    /**
     * Update voucher
     */
    public function updateVoucher(int $voucherId, array $data): bool {
        global $wpdb;
        
        // Validate required fields
        if (empty($data['name'])) {
            return false;
        }

        // Prepare data
        $voucherData = [
            'type' => $this->formatMultiSelect($data['type'] ?? []),
            'status' => $data['status'] ?? 'expose',
            'category' => $this->formatMultiSelect($data['category'] ?? []),
            'image' => $data['image'] ?? null,
            'name' => sanitize_text_field($data['name']),
            'short_description' => sanitize_textarea_field($data['short_description'] ?? ''),
            'detail_description' => wp_kses_post($data['detail_description'] ?? ''),
        ];

        // Get table name
        $table_name = VoucherDatabase::getTableName();
        
        // Update voucher
        $result = $wpdb->update(
            $table_name,
            $voucherData,
            ['id' => $voucherId],
            [
                '%s', // type
                '%s', // status
                '%s', // category
                '%s', // image
                '%s', // name
                '%s', // short_description
                '%s'  // detail_description
            ],
            ['%d'] // id
        );
        
        if ($result !== false) {
            // Clear cache
            $this->clearCache();
            wp_cache_delete($this->getCacheKey($voucherId));
            return true;
        }

        return false;
    }

    /**
     * Delete voucher
     */
    public function deleteVoucher(int $voucherId): bool {
        global $wpdb;
        
        // Get table name
        $table_name = VoucherDatabase::getTableName();
        
        // Delete voucher
        $result = $wpdb->delete(
            $table_name,
            ['id' => $voucherId],
            ['%d'] // id
        );
        
        if ($result !== false) {
            // Clear cache
            $this->clearCache();
            wp_cache_delete($this->getCacheKey($voucherId));
            return true;
        }

        return false;
    }

    /**
     * Get vouchers by status
     */
    public function getVouchersByStatus(string $status): array {
        global $wpdb;
        $table_name = VoucherDatabase::getTableName();
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE status = %s ORDER BY created_at DESC",
            $status
        ));
        
        return array_map(function($result) {
            return (array) $result;
        }, $results);
    }

    /**
     * Get vouchers by type
     */
    public function getVouchersByType(string $type): array {
        global $wpdb;
        $table_name = VoucherDatabase::getTableName();
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE type LIKE %s ORDER BY created_at DESC",
            '%' . $wpdb->esc_like($type) . '%'
        ));
        
        return array_map(function($result) {
            return (array) $result;
        }, $results);
    }

    /**
     * Get vouchers by category - using membership ID
     */
    public function getVouchersByCategory(string $categoryId, string $status = 'expose'): array {
        global $wpdb;
        $table_name = VoucherDatabase::getTableName();
        
        // Validate that categoryId is numeric
        if (!is_numeric($categoryId)) {
            return [];
        }
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE FIND_IN_SET(%s, category) > 0 AND status = %s ORDER BY created_at DESC",
            $categoryId,
            $status
        ));
        
        return array_map(function($result) {
            return (array) $result;
        }, $results);
    }

    /**
     * Get exposed vouchers for frontend
     */
    public function getExposedVouchers(int $limit = 10): array {
        return $this->getVouchersByStatus('expose');
    }

    /**
     * Format multi-select values to comma-separated string
     */
    private function formatMultiSelect($values): string {
        if (empty($values)) {
            return '';
        }
        
        if (is_string($values)) {
            return $values;
        }
        
        if (is_array($values)) {
            return implode(',', array_filter($values));
        }
        
        return '';
    }

    /**
     * Get cache key for voucher
     */
    private function getCacheKey(int $voucherId): string {
        return self::CACHE_PREFIX . $voucherId;
    }

    /**
     * Clear all voucher cache
     */
    private function clearCache(): void {
        wp_cache_flush_group(self::CACHE_PREFIX);
    }
    
    /**
     * Search vouchers by name
     */
    public function searchVouchersByName(string $searchTerm, int $limit = 10, ?string $filterType = null): array {
        global $wpdb;
        $table_name = VoucherDatabase::getTableName();
        
        $where_conditions = ["name LIKE %s", "status = 'expose'"];
        $where_values = ['%' . $wpdb->esc_like($searchTerm) . '%'];
        
        // Add type filter if specified
        if ($filterType) {
            $where_conditions[] = "type LIKE %s";
            $where_values[] = '%' . $wpdb->esc_like($filterType) . '%';
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT id, name, short_description FROM $table_name 
             WHERE $where_clause 
             ORDER BY created_at DESC 
             LIMIT %d",
            array_merge($where_values, [$limit])
        ));
        
        return array_map(function($result) {
            return (array) $result;
        }, $results);
    }
}
