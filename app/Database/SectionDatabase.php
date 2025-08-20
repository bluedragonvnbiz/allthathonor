<?php

namespace App\Database;

class SectionDatabase {
    private static $table_name = 'section_data';

    /**
     * Create database table on theme activation
     */
    public static function createTable() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            page varchar(100) NOT NULL DEFAULT 'home',
            section_key varchar(255) NOT NULL,
            data longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY page_section (page, section_key)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Add page column to existing table (for migration)
     */
    public static function addPageColumn() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        
        // Check if page column already exists
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'page'");
        
        if (empty($column_exists)) {
            // Add page column with default value
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN page varchar(100) NOT NULL DEFAULT 'home' AFTER id");
            
            // Update unique key if old one exists
            $index_exists = $wpdb->get_results("SHOW INDEX FROM $table_name WHERE Key_name = 'section_key'");
            if (!empty($index_exists)) {
                $wpdb->query("ALTER TABLE $table_name DROP INDEX section_key");
            }
            
            // Add new composite unique key
            $wpdb->query("ALTER TABLE $table_name ADD UNIQUE KEY page_section (page, section_key)");
        }
    }

    /**
     * Migrate existing data to have page column
     */
    public static function migrateExistingData() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::$table_name;
        
        // Update all existing records to have page = 'home'
        $wpdb->query("UPDATE $table_name SET page = 'home' WHERE page = '' OR page IS NULL");
    }

    /**
     * Run complete migration
     */
    public static function runMigration() {
        self::addPageColumn();
        self::migrateExistingData();
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
