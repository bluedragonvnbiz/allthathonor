<?php

use App\Database\SectionDatabase;
use App\Database\ProductDatabase;
use App\Database\VoucherDatabase;
use App\Database\InquiryDatabase;

// Create database tables on theme activation
add_action('after_switch_theme', [SectionDatabase::class, 'createTable']);
add_action('after_switch_theme', [ProductDatabase::class, 'createTable']);
add_action('after_switch_theme', [VoucherDatabase::class, 'createTable']);
add_action('after_switch_theme', [InquiryDatabase::class, 'createTable']);
