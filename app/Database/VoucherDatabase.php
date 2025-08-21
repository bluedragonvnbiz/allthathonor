<?php

namespace App\Database;

class VoucherDatabase {
    private static $table_name = 'vouchers';

    /**
     * Create database table on theme activation
     */
    public static function createTable() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            type varchar(255) DEFAULT 'VOUCHER,EVENT_INVITATION' COMMENT 'VOUCHER, EVENT_INVITATION (có thể chọn cả 2)',
            status enum('expose','not_expose') DEFAULT 'expose' COMMENT '노출/미노출',
            category varchar(255) DEFAULT 'PRIME,SIGNATURE' COMMENT 'PRIME, SIGNATURE (có thể chọn cả 2)',
            image varchar(500) DEFAULT NULL COMMENT 'Hình ảnh đại diện',
            name varchar(255) NOT NULL COMMENT 'Tên voucher',
            short_description text COMMENT 'Mô tả ngắn',
            detail_description longtext COMMENT 'Mô tả chi tiết (rich text)',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Drop table on theme deactivation
     * Note: We don't actually drop the table to preserve data
     */
    public static function dropTable() {
        // Do nothing - preserve voucher data when switching themes
        // global $wpdb;
        // $table_name = $wpdb->prefix . self::$table_name;
        // $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

    /**
     * Get table name with prefix
     */
    public static function getTableName() {
        global $wpdb;
        return $wpdb->prefix . self::$table_name;
    }

    /**
     * Check if table exists
     */
    public static function tableExists() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        $result = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
        return $result === $table_name;
    }
}
