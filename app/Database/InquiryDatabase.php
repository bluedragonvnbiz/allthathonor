<?php

namespace App\Database;

class InquiryDatabase {
    
    const TABLE_NAME = 'inquiries';
    
    /**
     * Get table name with WordPress prefix
     */
    public static function getTableName(): string {
        global $wpdb;
        return $wpdb->prefix . self::TABLE_NAME;
    }
    
    /**
     * Create inquiry table
     */
    public static function createTable(): void {
        global $wpdb;
        
        $table_name = self::getTableName();
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            inquiry_number varchar(20) NOT NULL,
            corporate_name varchar(255) NOT NULL,
            contact_person varchar(100) NOT NULL,
            contact_phone varchar(20) NOT NULL,
            email varchar(255) NOT NULL,
            category_main varchar(100) NOT NULL,
            category_sub varchar(100) NOT NULL,
            inquiry_content text NOT NULL,
            status enum('unanswered', 'answered') DEFAULT 'unanswered',
            registration_date datetime DEFAULT CURRENT_TIMESTAMP,
            answer_date datetime NULL,
            answer_content text NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY inquiry_number (inquiry_number),
            KEY status (status),
            KEY category_main (category_main),
            KEY category_sub (category_sub),
            KEY registration_date (registration_date)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Insert sample data if table is empty
        if ($wpdb->get_var("SELECT COUNT(*) FROM $table_name") == 0) {
            self::insertSampleData();
        }
    }
    
    /**
     * Insert sample data
     */
    private static function insertSampleData(): void {
        global $wpdb;
        
        $table_name = self::getTableName();
        
        $sample_data = [
            [
                'inquiry_number' => 'QN000007',
                'corporate_name' => '주식회사 홍길동',
                'contact_person' => '홍길동',
                'contact_phone' => '010-1234-5678',
                'email' => 'company@gmail.com',
                'category_main' => '멤버십',
                'category_sub' => '이용권 사용방법',
                'inquiry_content' => '멤버십 이용권 사용 방법에 대해 문의드립니다.',
                'status' => 'unanswered',
                'registration_date' => '2025-01-20 10:00:00'
            ],
            [
                'inquiry_number' => 'QN000008',
                'corporate_name' => '주식회사 홍길동',
                'contact_person' => '홍길동',
                'contact_phone' => '010-1234-5678',
                'email' => 'company@gmail.com',
                'category_main' => '멤버십',
                'category_sub' => '이용권 사용방법',
                'inquiry_content' => '멤버십 이용권 사용 방법에 대해 문의드립니다.',
                'status' => 'answered',
                'registration_date' => '2025-01-20 10:00:00',
                'answer_date' => '2025-01-20 14:00:00',
                'answer_content' => '멤버십 이용권 사용 방법에 대한 답변입니다.'
            ],
            [
                'inquiry_number' => 'QN000009',
                'corporate_name' => '주식회사 홍길동',
                'contact_person' => '홍길동',
                'contact_phone' => '010-1234-5678',
                'email' => 'company@gmail.com',
                'category_main' => '멤버십',
                'category_sub' => '이용권 사용방법',
                'inquiry_content' => '멤버십 이용권 사용 방법에 대해 문의드립니다.',
                'status' => 'unanswered',
                'registration_date' => '2025-01-20 10:00:00'
            ],
            [
                'inquiry_number' => 'QN000010',
                'corporate_name' => '주식회사 홍길동',
                'contact_person' => '홍길동',
                'contact_phone' => '010-1234-5678',
                'email' => 'company@gmail.com',
                'category_main' => '멤버십',
                'category_sub' => '이용권 사용방법',
                'inquiry_content' => '멤버십 이용권 사용 방법에 대해 문의드립니다.',
                'status' => 'unanswered',
                'registration_date' => '2025-01-20 10:00:00'
            ],
            [
                'inquiry_number' => 'QN000011',
                'corporate_name' => '주식회사 홍길동',
                'contact_person' => '홍길동',
                'contact_phone' => '010-1234-5678',
                'email' => 'company@gmail.com',
                'category_main' => '멤버십',
                'category_sub' => '이용권 사용방법',
                'inquiry_content' => '멤버십 이용권 사용 방법에 대해 문의드립니다.',
                'status' => 'unanswered',
                'registration_date' => '2025-01-20 10:00:00'
            ]
        ];
        
        foreach ($sample_data as $data) {
            $wpdb->insert($table_name, $data);
        }
    }
    
    /**
     * Drop table (for theme deactivation)
     */
    public static function dropTable(): void {
        global $wpdb;
        
        $table_name = self::getTableName();
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}
