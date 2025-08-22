<?php

namespace App\Database;

class MembershipDatabase {
    
    public static function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'memberships';
    }
    
    public static function createTable() {
        global $wpdb;
        
        $table_name = self::getTableName();
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            membership_number varchar(50) NOT NULL,
            membership_name varchar(100) NOT NULL,
            status enum('expose','not_expose') NOT NULL DEFAULT 'expose',
            top_phrase varchar(255) DEFAULT NULL,
            image varchar(255) DEFAULT NULL,
            sale_price decimal(15,2) NOT NULL DEFAULT 0.00,
            summary_description text,
            notes longtext,
            usage_guide longtext,
            travel_care_usage_guide longtext,
            lifestyle_usage_guide longtext,
            special_benefit_usage_guide longtext,
            welcome_gift_usage_guide longtext,
            travel_care_vouchers json,
            lifestyle_vouchers json,
            special_benefit_vouchers json,
            welcome_gift_vouchers json,
            sort_order int(11) NOT NULL DEFAULT 0,
            is_featured tinyint(1) NOT NULL DEFAULT 0,
            valid_from date DEFAULT NULL,
            valid_to date DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY membership_number (membership_number),
            KEY status (status),
            KEY sort_order (sort_order),
            KEY is_featured (is_featured)
        ) $charset_collate;";

        // Insert sample data if table is empty
        if ($wpdb->get_var("SELECT COUNT(*) FROM $table_name") == 0) {
            self::insertSampleData();
        }
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public static function dropTable() {
        global $wpdb;
        $table_name = self::getTableName();
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

    /**
     * Insert sample data for PRIME and SIGNATURE memberships
     */
    public static function insertSampleData() {
        global $wpdb;
        $table_name = self::getTableName();
        
        // Check if sample data already exists
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE membership_name IN ('PRIME', 'SIGNATURE')");
        if ($existing > 0) {
            return; // Sample data already exists
        }
        
        $sampleData = [
            [
                'membership_number' => 'MB000001',
                'membership_name' => 'PRIME',
                'status' => 'expose',
                'top_phrase' => '핵심 혜택 중심의 실속형 패키지를 제공',
                'image' => '',
                'sale_price' => 5000000.00,
                'summary_description' => 'THE HERITAGE TRAVEL CLUB의 PRIME 멤버십으로 핵심 혜택 중심의 실속형 패키지를 제공합니다.',
                'notes' => '카드 연회비: 5,000,000원 (세금 별도)\n이용 기간: 가입 후 서비스 시작일로부터 1년\n가입문의: 02-1234-5678 혹은 웹사이트 1:1 문의',
                'usage_guide' => 'PRIME 멤버십 이용 안내서입니다.',
                'travel_care_usage_guide' => 'PRIME Travel Care 이용 안내:\n• 항공권 예약 시 30일 전 예약 필수\n• 연간 최대 3회 이용 가능\n• 부가서비스는 별도 문의',
                'lifestyle_usage_guide' => 'PRIME Lifestyle 이용 안내:\n• 레스토랑 예약은 7일 전 예약 필수\n• 연간 최대 5회 이용 가능\n• 특별 이벤트는 사전 공지',
                'special_benefit_usage_guide' => 'PRIME Special Benefit 이용 안내:\n• 호텔 업그레이드는 체크인 당일 확인\n• 연간 최대 2회 이용 가능\n• 블랙아웃 데이트 제한 있음',
                'welcome_gift_usage_guide' => 'PRIME Welcome Gift 이용 안내:\n• 가입 후 30일 이내 수령\n• 1회만 제공\n• 배송은 5-7일 소요',
                'travel_care_vouchers' => '[{"id": 1, "is_summary": true}, {"id": 2, "is_summary": true}, {"id": 3, "is_summary": true}]',
                'lifestyle_vouchers' => '[{"id": 10, "is_summary": true}, {"id": 11, "is_summary": false}, {"id": 12, "is_summary": true}]',
                'special_benefit_vouchers' => '[{"id": 4, "is_summary": true}, {"id": 5, "is_summary": true}, {"id": 6, "is_summary": true}]',
                'welcome_gift_vouchers' => '[{"id": 10, "is_summary": true}]',
                'welcome_gift_vouchers' => '[]',
                'sort_order' => 1,
                'is_featured' => 1,
                'valid_from' => date('Y-m-d'),
                'valid_to' => date('Y-m-d', strtotime('+1 year'))
            ],
            [
                'membership_number' => 'MB000002',
                'membership_name' => 'SIGNATURE',
                'status' => 'expose',
                'top_phrase' => '전담 매니저를 상시 배정하여 전 여정 엄선된 맞춤형 서비스를 제공',
                'image' => '',
                'sale_price' => 10000000.00,
                'summary_description' => 'THE HERITAGE TRAVEL CLUB의 SIGNATURE 멤버십으로 전담 매니저를 상시 배정하여 전 여정 엄선된 맞춤형 서비스를 제공합니다.',
                'notes' => '카드 연회비: 10,000,000원 (세금 별도)\n이용 기간: 가입 후 서비스 시작일로부터 1년\n가입문의: 02-1234-5678 혹은 웹사이트 1:1 문의',
                'usage_guide' => 'SIGNATURE 멤버십 이용 안내서입니다.',
                'travel_care_usage_guide' => 'SIGNATURE Travel Care 이용 안내:\n• 전담 매니저가 24시간 대기\n• 항공권 예약 시 7일 전 예약 가능\n• 연간 무제한 이용\n• VIP 라운지 무료 이용',
                'lifestyle_usage_guide' => 'SIGNATURE Lifestyle 이용 안내:\n• 전담 매니저가 모든 예약 대행\n• 연간 무제한 이용\n• 특별 이벤트 우선 예약\n• VIP 서비스 제공',
                'special_benefit_usage_guide' => 'SIGNATURE Special Benefit 이용 안내:\n• 전담 매니저가 모든 혜택 관리\n• 연간 무제한 이용\n• 블랙아웃 데이트 없음\n• 프리미엄 서비스 제공',
                'welcome_gift_usage_guide' => 'SIGNATURE Welcome Gift 이용 안내:\n• 가입 즉시 수령 가능\n• 전담 매니저가 배송 관리\n• 프리미엄 패키지 제공\n• 개인화된 서비스',
                'travel_care_vouchers' => '[{"id": 6, "is_summary": true}, {"id": 7, "is_summary": false}]',
                'lifestyle_vouchers' => '[{"id": 8, "is_summary": true}, {"id": 9, "is_summary": false}]',
                'special_benefit_vouchers' => '[]',
                'welcome_gift_vouchers' => '[]',
                'sort_order' => 2,
                'is_featured' => 1,
                'valid_from' => date('Y-m-d'),
                'valid_to' => date('Y-m-d', strtotime('+1 year'))
            ]
        ];
        
        foreach ($sampleData as $data) {
            $wpdb->insert(
                $table_name,
                $data,
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
                    '%s', // travel_care_usage_guide
                    '%s', // lifestyle_usage_guide
                    '%s', // special_benefit_usage_guide
                    '%s', // welcome_gift_usage_guide
                    '%s', // travel_care_vouchers
                    '%s', // lifestyle_vouchers
                    '%s', // special_benefit_vouchers
                    '%s', // welcome_gift_vouchers
                    '%d', // sort_order
                    '%d', // is_featured
                    '%s', // valid_from
                    '%s'  // valid_to
                ]
            );
        }
    }
}
