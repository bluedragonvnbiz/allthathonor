<?php

use App\Database\SectionDatabase;
use App\Database\ProductDatabase;
use App\Database\VoucherDatabase;
use App\Database\InquiryDatabase;
use App\Database\MembershipDatabase;

// Create database tables on theme activation
add_action('after_switch_theme', [SectionDatabase::class, 'createTable']);
add_action('after_switch_theme', [ProductDatabase::class, 'createTable']);
add_action('after_switch_theme', [VoucherDatabase::class, 'createTable']);
add_action('after_switch_theme', [InquiryDatabase::class, 'createTable']);
add_action('after_switch_theme', [MembershipDatabase::class, 'createTable']);

// Insert sample data after table creation
add_action('after_switch_theme', function() {
    // Wait a bit for tables to be created
    add_action('init', function() {
        MembershipDatabase::insertSampleData();
    }, 20);
});
