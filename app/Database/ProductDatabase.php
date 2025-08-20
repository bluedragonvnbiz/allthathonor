<?php

namespace App\Database;

class ProductDatabase {
    private static $table_name = 'products';

    /**
     * Create database table on theme activation
     */
    public static function createTable() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            exposure_status varchar(20) NOT NULL DEFAULT 'expose',
            main_image varchar(255),
            product_name varchar(255) NOT NULL,
            product_name_en varchar(255),
            summary_description text,
            detailed_description longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Drop table on theme deactivation
     */
    public static function dropTable() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

    /**
     * Get table name with prefix
     */
    public static function getTableName() {
        global $wpdb;
        return $wpdb->prefix . self::$table_name;
    }
}
