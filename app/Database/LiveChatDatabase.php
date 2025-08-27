<?php

namespace App\Database;

class LiveChatDatabase {
    
    /**
     * Get sessions table name
     */
    public static function getSessionTableName() {
        global $wpdb;
        return $wpdb->prefix . 'livechat_sessions';
    }
    
    /**
     * Get messages table name
     */
    public static function getMessageTableName() {
        global $wpdb;
        return $wpdb->prefix . 'livechat_messages';
    }
    
    /**
     * Create database tables for LiveChat
     */
    public static function createTable() {
        global $wpdb;
        
        $session_table = self::getSessionTableName();
        $message_table = self::getMessageTableName();
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Create sessions table with category support
        $session_sql = "CREATE TABLE $session_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id varchar(64) NOT NULL,
            client_connected tinyint(1) NOT NULL DEFAULT 0,
            customer_name varchar(100) DEFAULT NULL,
            customer_email varchar(100) DEFAULT NULL,
            customer_phone varchar(20) DEFAULT NULL,
            category_main varchar(100) DEFAULT NULL,
            category_sub varchar(100) DEFAULT NULL,
            chat_stage enum('category_main','category_sub','chat_active','closed') NOT NULL DEFAULT 'category_main',
            status enum('waiting','active','closed') NOT NULL DEFAULT 'waiting',
            admin_user_id bigint(20) DEFAULT NULL,
            welcome_message_sent tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            closed_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY session_id (session_id),
            KEY status (status),
            KEY chat_stage (chat_stage),
            KEY admin_user_id (admin_user_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Create messages table with message types
        $message_sql = "CREATE TABLE $message_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id varchar(64) NOT NULL,
            sender_type enum('customer','admin','system') NOT NULL,
            message_type enum('text','system','welcome','category_info') NOT NULL DEFAULT 'text',
            sender_name varchar(100) DEFAULT NULL,
            message text NOT NULL,
            metadata json DEFAULT NULL,
            is_read tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY session_id (session_id),
            KEY sender_type (sender_type),
            KEY message_type (message_type),
            KEY is_read (is_read),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($session_sql);
        dbDelta($message_sql);
    }
}