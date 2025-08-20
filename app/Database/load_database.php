<?php

use App\Database\SectionDatabase;
use App\Database\ProductDatabase;

// Create database tables on theme activation
// add_action('after_switch_theme', [SectionDatabase::class, 'createTable']);
// add_action('after_switch_theme', [ProductDatabase::class, 'createTable']);

// // Drop tables on theme deactivation
// add_action('switch_theme', [SectionDatabase::class, 'dropTable']);
// add_action('switch_theme', [ProductDatabase::class, 'dropTable']);
