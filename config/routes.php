<?php
/**
 * Routes Configuration
 * Using wp_route() helper function for fluent route definition
 */
// Define routes using wp_route() helper

// Home page
wp_route('home')->action('index@HomeController')->register();

// Inquiry pages
wp_route('inquiry')->action('index@InquiryController')->register();

// Inquiry management page
wp_route('management/inquiry')->action('management@InquiryController')->admin()->register();
wp_route('management/inquiry-view')->action('view@InquiryController')->admin()->register();

// Membership management page
wp_route('management/membership')->action('management@MembershipController')->admin()->register();
wp_route('management/membership-view')->action('view@MembershipController')->admin()->register();

// Membership pages
wp_route('membership')->action('index@MembershipController')->register();

// Auth pages
wp_route('login')->action('login@AccountController')->register();

// Section management page
wp_route('management')->action('index@ManagementController')->admin()->register();
wp_route('management/edit')->action('edit@ManagementController')->admin()->register();

// Product management page
wp_route('product')->action('index@ProductController')->admin()->register();
wp_route('product/add')->action('add@ProductController')->admin()->register();
wp_route('product/edit')->action('edit@ProductController')->admin()->register();
wp_route('product/view')->action('view@ProductController')->admin()->register();

// Voucher management page
wp_route('voucher')->action('index@VoucherController')->admin()->register();
wp_route('voucher/add')->action('add@VoucherController')->admin()->register();
wp_route('voucher/edit')->action('edit@VoucherController')->admin()->register();
wp_route('voucher/view')->action('view@VoucherController')->admin()->register();

