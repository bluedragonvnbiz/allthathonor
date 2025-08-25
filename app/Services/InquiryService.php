<?php

namespace App\Services;

use App\Database\InquiryDatabase;

class InquiryService {
    
    private $table_name;
    
    public function __construct() {
        $this->table_name = InquiryDatabase::getTableName();
    }
    
    /**
     * Get inquiries with search, filter and pagination
     */
    public function getInquiries($params = []) {
        global $wpdb;
        
        // Default parameters
        $searchType = $params['search_type'] ?? 'corporate_name';
        $searchKeyword = $params['search_keyword'] ?? '';
        $categoryMain = $params['category_main'] ?? '';
        $categorySub = $params['category_sub'] ?? '';
        $statusFilter = $params['status'] ?? [];
        $page = max(1, intval($params['current_page'] ?? 1));
        $per_page = $params['per_page'] ?? 10;
        
        // Build WHERE clause
        $where_conditions = [];
        $where_values = [];
        
        if (!empty($searchKeyword)) {
            $where_conditions[] = "$searchType LIKE %s";
            $where_values[] = '%' . $wpdb->esc_like($searchKeyword) . '%';
        }
        
        if (!empty($categoryMain)) {
            $where_conditions[] = "category_main = %s";
            $where_values[] = $categoryMain;
        }
        
        if (!empty($categorySub)) {
            $where_conditions[] = "category_sub = %s";
            $where_values[] = $categorySub;
        }
        
        if (!empty($statusFilter)) {
            // Check if 'all' is in the status filter
            if (in_array('all', $statusFilter)) {
                // If 'all' is selected, don't apply any status filter (show all)
                // Do nothing - no status filter will be applied
            } else {
                // Apply status filter only if 'all' is not selected
                $status_placeholders = array_fill(0, count($statusFilter), '%s');
                $where_conditions[] = "status IN (" . implode(',', $status_placeholders) . ")";
                $where_values = array_merge($where_values, $statusFilter);
            }
        }
        
        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }
        
        // Pagination
        $offset = ($page - 1) * $per_page;
        
        // Get total count
        $count_query = "SELECT COUNT(*) FROM {$this->table_name} $where_clause";
        if (!empty($where_values)) {
            $count_query = $wpdb->prepare($count_query, $where_values);
        }
        $totalInquiries = $wpdb->get_var($count_query);
        $totalPages = ceil($totalInquiries / $per_page);
        
        // Get inquiries
        $query = "SELECT * FROM {$this->table_name} $where_clause ORDER BY registration_date DESC LIMIT %d OFFSET %d";
        $query_values = array_merge($where_values, [$per_page, $offset]);
        $inquiries = $wpdb->get_results($wpdb->prepare($query, $query_values), ARRAY_A);
        
        return [
            'inquiries' => $inquiries,
            'total_inquiries' => $totalInquiries,
            'total_pages' => $totalPages,
            'current_page' => $page,
            'per_page' => $per_page
        ];
    }
    
    /**
     * Get categories from configuration (same as inquiry-form.js)
     */
    public function getCategories() {
        $categories = [
            '멤버십' => [
                '이용권 사용방법' => '이용권 사용방법',
                '멤버십 혜택' => '멤버십 혜택',
                '멤버십 가입' => '멤버십 가입',
                '멤버십 해지' => '멤버십 해지',
                '기타' => '기타'
            ],
            '상품' => [
                '상품 문의' => '상품 문의',
                '상품 구매' => '상품 구매',
                '상품 환불' => '상품 환불',
                '기타' => '기타'
            ],
            '서비스' => [
                '서비스 이용' => '서비스 이용',
                '서비스 문의' => '서비스 문의',
                '기타' => '기타'
            ],
            '기타' => [
                '일반 문의' => '일반 문의',
                '불만 사항' => '불만 사항',
                '제안 사항' => '제안 사항',
                '기타' => '기타'
            ]
        ];
        
        return [
            'categories' => $categories,
            'main_categories' => array_keys($categories),
            'sub_categories' => $this->getAllSubCategories($categories)
        ];
    }
    
    /**
     * Get all sub categories from configuration
     */
    private function getAllSubCategories($categories) {
        $subCategories = [];
        foreach ($categories as $mainCategory => $subCategoryList) {
            foreach ($subCategoryList as $subCategory) {
                $subCategories[] = $subCategory;
            }
        }
        return array_unique($subCategories);
    }
    
    /**
     * Get sub categories for a specific main category
     */
    public function getSubCategories($mainCategory) {
        $categories = $this->getCategories()['categories'];
        return isset($categories[$mainCategory]) ? array_values($categories[$mainCategory]) : [];
    }
    
    /**
     * Get search parameters from GET request
     */
    public function getSearchParams() {
        return [
            'search_type' => $_GET['search_type'] ?? 'corporate_name',
            'search_keyword' => $_GET['search_keyword'] ?? '',
            'category_main' => $_GET['category_main'] ?? '',
            'category_sub' => $_GET['category_sub'] ?? '',
            'status' => $_GET['status'] ?? [],
            'current_page' => max(1, intval($_GET['current_page'] ?? 1))
        ];
    }
    
    /**
     * Format inquiry data for display
     */
    public function formatInquiry($inquiry) {
        return [
            'id' => $inquiry['id'],
            'inquiry_number' => htmlspecialchars($inquiry['inquiry_number']),
            'inquiry_content'=> nl2br(htmlspecialchars($inquiry['inquiry_content'])),
            'corporate_name' => htmlspecialchars($inquiry['corporate_name']),
            'contact_person' => htmlspecialchars($inquiry['contact_person']),
            'contact_phone' => htmlspecialchars($inquiry['contact_phone']),
            'email' => htmlspecialchars($inquiry['email']),
            'category_main' => htmlspecialchars($inquiry['category_main']),
            'category_sub' => htmlspecialchars($inquiry['category_sub']),
            'category_display' => htmlspecialchars($inquiry['category_main']) . ' > ' . htmlspecialchars($inquiry['category_sub']),
            'registration_date' => date('Y.m.d', strtotime($inquiry['registration_date'])),
            'status' => $inquiry['status'],
            'status_display' => $inquiry['status'] === 'unanswered' ? '미답변' : '답변완료',
            'answer_content' => nl2br(htmlspecialchars($inquiry['answer_content'])),
            'answer_date' => $inquiry['answer_date'] ? date('Y.m.d', strtotime($inquiry['answer_date'])) : '-'
        ];
    }
    
    /**
     * Get pagination data
     */
    public function getPaginationData($currentPage, $totalPages) {
        if ($totalPages <= 5) {
            return [
                'type' => 'simple',
                'pages' => range(1, $totalPages)
            ];
        } else {
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);
            
            $pages = [];
            
            // Always show first page
            if ($startPage > 1) {
                $pages[] = ['number' => 1, 'type' => 'page'];
                if ($startPage > 2) {
                    $pages[] = ['type' => 'ellipsis'];
                }
            }
            
            // Show current range
            for ($i = $startPage; $i <= $endPage; $i++) {
                $pages[] = ['number' => $i, 'type' => 'page'];
            }
            
            // Always show last page
            if ($endPage < $totalPages) {
                if ($endPage < $totalPages - 1) {
                    $pages[] = ['type' => 'ellipsis'];
                }
                $pages[] = ['number' => $totalPages, 'type' => 'page'];
            }
            
            return [
                'type' => 'smart',
                'pages' => $pages
            ];
        }
    }
    
    /**
     * Get inquiry by ID
     */
    public function getInquiryById($id) {
        global $wpdb;
        
        $inquiry = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id),
            ARRAY_A
        );
        
        if (!$inquiry) {
            return null;
        }
        
        return $this->formatInquiry($inquiry);
    }

    /**
     * Update inquiry answer
     */
    public function updateInquiryAnswer($inquiryId, $answerContent) {
        global $wpdb;
        
        $result = $wpdb->update(
            $this->table_name,
            [
                'answer_content' => $answerContent,
                'status' => 'answered',
                'answer_date' => current_time('mysql')
            ],
            ['id' => $inquiryId],
            ['%s', '%s', '%s'],
            ['%d']
        );
        
        return $result !== false;
    }
}
