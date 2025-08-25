<?php

namespace App\Services;

use App\Database\MembershipDatabase;

class MembershipService {
    private const CACHE_PREFIX = 'membership:';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get membership by ID
     */
    public function getMembership(int $membershipId) {
        // Try to get from cache first
        $cached = wp_cache_get($this->getCacheKey($membershipId));
        if ($cached !== false) {
            return $cached;
        }

        global $wpdb;
        $table_name = MembershipDatabase::getTableName();
        
        $membership = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $membershipId
        ), ARRAY_A);
        
        if ($membership) {
            // Parse JSON fields
            $membership = $this->parseJsonFields($membership);
            // Cache the result
            wp_cache_set($this->getCacheKey($membershipId), $membership, '', self::CACHE_TTL);
        }

        return $membership;
    }

    /**
     * Get all memberships with search and filter
     */
    public function getAllMemberships(string $searchKeyword = '', $statusFilter = [], int $page = 1, int $perPage = 10): array {
        global $wpdb;
        $table_name = MembershipDatabase::getTableName();
        
        $where_conditions = [];
        $where_values = [];
        
        // Search by keyword
        if (!empty($searchKeyword)) {
            $where_conditions[] = "(membership_name LIKE %s OR membership_number LIKE %s OR summary_description LIKE %s)";
            $search_term = '%' . $wpdb->esc_like($searchKeyword) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }
        
        // Filter by status
        if (!empty($statusFilter)) {
            if (!in_array('all', $statusFilter)) {
                $status_placeholders = array_fill(0, count($statusFilter), '%s');
                $where_conditions[] = "status IN (" . implode(',', $status_placeholders) . ")";
                $where_values = array_merge($where_values, $statusFilter);
            }
        }
        
        // Build query
        $query = "SELECT * FROM $table_name";
        if (!empty($where_conditions)) {
            $query .= " WHERE " . implode(' AND ', $where_conditions);
        }
        $query .= " ORDER BY sort_order ASC, created_at DESC";
        
        // Add pagination
        $offset = ($page - 1) * $perPage;
        $query .= " LIMIT $perPage OFFSET $offset";
        
        // Prepare and execute query
        if (!empty($where_values)) {
            $results = $wpdb->get_results($wpdb->prepare($query, $where_values));
        } else {
            $results = $wpdb->get_results($query);
        }

        // Parse JSON fields for each result
        $results = array_map(function($result) {
            return $this->parseJsonFields((array) $result);
        }, $results);

        return $results;
    }
    
    /**
     * Get total count for pagination
     */
    public function getTotalMemberships(string $searchKeyword = '', $statusFilter = []): int {
        global $wpdb;
        $table_name = MembershipDatabase::getTableName();
        
        $where_conditions = [];
        $where_values = [];
        
        // Search by keyword
        if (!empty($searchKeyword)) {
            $where_conditions[] = "(membership_name LIKE %s OR membership_number LIKE %s OR summary_description LIKE %s)";
            $search_term = '%' . $wpdb->esc_like($searchKeyword) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }
        
        // Filter by status
        if (!empty($statusFilter)) {
            if (!in_array('all', $statusFilter)) {
                $status_placeholders = array_fill(0, count($statusFilter), '%s');
                $where_conditions[] = "status IN (" . implode(',', $status_placeholders) . ")";
                $where_values = array_merge($where_values, $statusFilter);
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
     * Create new membership
     */
    public function createMembership(array $data): ?int {
        global $wpdb;

        // Validate required fields
        if (empty($data['membership_name'])) {
            return null;
        }

        // Generate membership number if not provided
        if (empty($data['membership_number'])) {
            $data['membership_number'] = $this->generateMembershipNumber();
        }

        // Prepare data
        $membershipData = [
            'membership_number' => sanitize_text_field($data['membership_number']),
            'membership_name' => sanitize_text_field($data['membership_name']),
            'status' => $data['status'] ?? 'expose',
            'top_phrase' => sanitize_text_field($data['top_phrase'] ?? ''),
            'image' => $data['image'] ?? null,
            'sale_price' => $this->formatPrice($data['sale_price'] ?? ''),
            'summary_description' => sanitize_textarea_field($data['summary_description'] ?? ''),
            'notes' => wp_kses_post($data['notes'] ?? ''),
            'usage_guide' => wp_kses_post($data['usage_guide'] ?? ''),
            'travel_care_vouchers' => $this->formatJsonField($data['travel_care_vouchers'] ?? []),
            'lifestyle_vouchers' => $this->formatJsonField($data['lifestyle_vouchers'] ?? []),
            'special_benefit_vouchers' => $this->formatJsonField($data['special_benefit_vouchers'] ?? []),
            'welcome_gift_vouchers' => $this->formatJsonField($data['welcome_gift_vouchers'] ?? []),
            'sort_order' => intval($data['sort_order'] ?? 0),
            'is_featured' => !empty($data['is_featured']) ? 1 : 0,
            'valid_from' => $data['valid_from'] ?? null,
            'valid_to' => $data['valid_to'] ?? null,
        ];

        // Get table name
        $table_name = MembershipDatabase::getTableName();
        
        // Insert membership
        $result = $wpdb->insert(
            $table_name,
            $membershipData,
            [
                '%s', // membership_number
                '%s', // membership_name
                '%s', // status
                '%s', // top_phrase
                '%s', // image
                '%f', // sale_price
                '%s', // summary_description
                '%s', // notes
                '%s', // usage_guide
                '%s', // travel_care_vouchers
                '%s', // lifestyle_vouchers
                '%s', // special_benefit_vouchers
                '%s', // welcome_gift_vouchers
                '%d', // sort_order
                '%d', // is_featured
                '%s', // valid_from
                '%s', // valid_to
            ]
        );
        
        if ($result !== false) {
            $membershipId = $wpdb->insert_id;
            // Clear cache
            $this->clearCache();
            return $membershipId;
        }

        return null;
    }

    /**
     * Update membership
     */
    public function updateMembership(int $membershipId, array $data): bool {
        global $wpdb;
        
        // Validate required fields
        if (empty($data['membership_name'])) {
            return false;
        }

        // Prepare data
        $membershipData = [
            'membership_name' => sanitize_text_field($data['membership_name']),
            'status' => $data['status'] ?? 'expose',
            'top_phrase' => sanitize_text_field($data['top_phrase'] ?? ''),
            'image' => $data['image'] ?? null,
            'sale_price' => $this->formatPrice($data['sale_price'] ?? ''),
            'summary_description' => sanitize_textarea_field($data['summary_description'] ?? ''),
            'notes' => wp_kses_post($data['notes'] ?? ''),
            'usage_guide' => wp_kses_post($data['usage_guide'] ?? ''),
            'travel_care_vouchers' => $this->formatJsonField($data['travel_care_vouchers'] ?? []),
            'lifestyle_vouchers' => $this->formatJsonField($data['lifestyle_vouchers'] ?? []),
            'special_benefit_vouchers' => $this->formatJsonField($data['special_benefit_vouchers'] ?? []),
            'welcome_gift_vouchers' => $this->formatJsonField($data['welcome_gift_vouchers'] ?? []),
            'sort_order' => intval($data['sort_order'] ?? 0),
            'is_featured' => !empty($data['is_featured']) ? 1 : 0,
            'valid_from' => $data['valid_from'] ?? null,
            'valid_to' => $data['valid_to'] ?? null,
        ];

        // Get table name
        $table_name = MembershipDatabase::getTableName();
        
        // Update membership
        $result = $wpdb->update(
            $table_name,
            $membershipData,
            ['id' => $membershipId],
            [
                '%s', // membership_name
                '%s', // status
                '%s', // top_phrase
                '%s', // image
                '%f', // sale_price
                '%s', // summary_description
                '%s', // notes
                '%s', // usage_guide
                '%s', // travel_care_vouchers
                '%s', // lifestyle_vouchers
                '%s', // special_benefit_vouchers
                '%s', // welcome_gift_vouchers
                '%d', // sort_order
                '%d', // is_featured
                '%s', // valid_from
                '%s', // valid_to
            ],
            ['%d'] // id
        );
        
        if ($result !== false) {
            // Clear cache
            $this->clearCache();
            wp_cache_delete($this->getCacheKey($membershipId));
            return true;
        }

        return false;
    }

    /**
     * Delete membership
     */
    public function deleteMembership(int $membershipId): bool {
        global $wpdb;
        
        // Get table name
        $table_name = MembershipDatabase::getTableName();
        
        // Delete membership
        $result = $wpdb->delete(
            $table_name,
            ['id' => $membershipId],
            ['%d'] // id
        );
        
        if ($result !== false) {
            // Clear cache
            $this->clearCache();
            wp_cache_delete($this->getCacheKey($membershipId));
            return true;
        }

        return false;
    }

    /**
     * Get memberships by status
     */
    public function getMembershipsByStatus(string $status): array {
        global $wpdb;
        $table_name = MembershipDatabase::getTableName();
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE status = %s ORDER BY sort_order ASC, created_at DESC",
            $status
        ));
        
        return array_map(function($result) {
            return $this->parseJsonFields((array) $result);
        }, $results);
    }

    /**
     * Get featured memberships
     */
    public function getFeaturedMemberships(int $limit = 10): array {
        global $wpdb;
        $table_name = MembershipDatabase::getTableName();
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE is_featured = 1 AND status = 'expose' ORDER BY sort_order ASC, created_at DESC LIMIT %d",
            $limit
        ));
        
        return array_map(function($result) {
            return $this->parseJsonFields((array) $result);
        }, $results);
    }

    /**
     * Get exposed memberships for frontend
     */
    public function getExposedMemberships(int $limit = 10): array {
        return $this->getMembershipsByStatus('expose');
    }

    /**
     * Search memberships by name
     */
    public function searchMembershipsByName(string $searchTerm, int $limit = 10): array {
        global $wpdb;
        $table_name = MembershipDatabase::getTableName();
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT id, membership_name, summary_description, image FROM $table_name 
             WHERE membership_name LIKE %s AND status = 'expose' 
             ORDER BY sort_order ASC, created_at DESC 
             LIMIT %d",
            '%' . $wpdb->esc_like($searchTerm) . '%',
            $limit
        ));
        
        return array_map(function($result) {
            return (array) $result;
        }, $results);
    }

    /**
     * Get vouchers for a specific membership category
     */
    public function getMembershipVouchers(int $membershipId, string $category): array {
        $membership = $this->getMembership($membershipId);
        if (!$membership) {
            return [];
        }

        $voucherIds = $membership[$category . '_vouchers'] ?? [];
        if (empty($voucherIds)) {
            return [];
        }

        // Get voucher details from VoucherService
        $voucherService = new VoucherService();
        $vouchers = [];
        
        foreach ($voucherIds as $voucherId) {
            $voucher = $voucherService->getVoucher($voucherId);
            if ($voucher) {
                $vouchers[] = $voucher;
            }
        }

        return $vouchers;
    }

    /**
     * Generate unique membership number
     */
    private function generateMembershipNumber(): string {
        global $wpdb;
        $table_name = MembershipDatabase::getTableName();
        
        $prefix = 'MB';
        $counter = 1;
        
        do {
            $membershipNumber = $prefix . str_pad($counter, 6, '0', STR_PAD_LEFT);
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE membership_number = %s",
                $membershipNumber
            ));
            $counter++;
        } while ($exists > 0);
        
        return $membershipNumber;
    }

    /**
     * Format price field
     */
    private function formatPrice($price): ?float {
        if (empty($price)) {
            return null;
        }
        
        // Remove non-numeric characters except decimal point
        $price = preg_replace('/[^0-9.]/', '', $price);
        
        return floatval($price);
    }

    /**
     * Format JSON field for database storage
     */
    private function formatJsonField($value): string {
        if (empty($value)) {
            return '[]';
        }
        
        if (is_string($value)) {
            return $value;
        }
        
        if (is_array($value)) {
            return json_encode(array_filter($value));
        }
        
        return '[]';
    }

    /**
     * Parse JSON fields from database
     */
    private function parseJsonFields(array $membership): array {
        $jsonFields = [
            'travel_care_vouchers',
            'lifestyle_vouchers', 
            'special_benefit_vouchers',
            'welcome_gift_vouchers'
        ];
        
        foreach ($jsonFields as $field) {
            if (isset($membership[$field])) {
                $decoded = json_decode($membership[$field], true);
                $membership[$field] = is_array($decoded) ? $decoded : [];
            } else {
                $membership[$field] = [];
            }
        }
        
        return $membership;
    }

    /**
     * Get cache key for membership
     */
    private function getCacheKey(int $membershipId): string {
        return self::CACHE_PREFIX . $membershipId;
    }

    /**
     * Get available grades for voucher filters
     */
    public function getAvailableGrades(): array {
        global $wpdb;
        $table_name = MembershipDatabase::getTableName();
        
        $results = $wpdb->get_results(
            "SELECT id as grade_id, membership_name as grade_name, sort_order
             FROM {$table_name} 
             WHERE status = 'expose' 
             ORDER BY sort_order ASC, membership_name ASC"
        );
        
        return $results ?: [];
    }

    /**
     * Clear all membership cache
     */
    private function clearCache(): void {
        wp_cache_flush_group(self::CACHE_PREFIX);
    }
}
