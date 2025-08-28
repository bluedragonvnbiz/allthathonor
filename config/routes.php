<?php
/**
 * Routes Configuration
 * Using wp_route() helper function for fluent route definition
 */

// Public routes
wp_route('home')->action('index@HomeController')->public()->register();
wp_route('inquiry')->action('index@InquiryController')->public()->register();
wp_route('membership')->action('index@MembershipController')->public()->register();
wp_route('login')->action('login@AccountController')->public()->register();
wp_route('register')->action('register@AccountController')->public()->register();
wp_route('register/payment-success')->action('payment_success@AccountController')->public()->register();
wp_route('admin')->action('admin@AccountController')->public()->register();

// LiveChat public routes
wp_route('chat/start')->action('startSession@LiveChatController')->public()->register();
wp_route('chat/subcategories')->action('getSubCategories@LiveChatController')->public()->register();
wp_route('chat/begin')->action('startChat@LiveChatController')->public()->register();
wp_route('chat/send')->action('sendMessage@LiveChatController')->public()->register();
wp_route('chat/stream')->action('streamMessages@LiveChatController')->public()->register();
wp_route('chat/history')->action('getChatHistory@LiveChatController')->public()->register();
wp_route('chat/close')->action('closeChat@LiveChatController')->public()->register();

// Admin routes
RouteBuilder::group([
    'prefix' => 'admin',
    'middleware' => ['AdminMiddleware'],
    'layout' => 'admin'
], function() {
    // Section management
    wp_route('section')->action('index@Admin\SectionController')->register();
    wp_route('section/edit')->action('edit@Admin\SectionController')->register();
    
    // Inquiry management
    wp_route('inquiry')->action('index@Admin\InquiryController')->register();
    wp_route('inquiry/view')->action('view@Admin\InquiryController')->register();
    
    // Membership management
    wp_route('membership')->action('index@Admin\MembershipController')->register();
    wp_route('membership/view')->action('view@Admin\MembershipController')->register();
    
    // Product management
    wp_route('product')->action('index@Admin\ProductController')->register();
    wp_route('product/add')->action('add@Admin\ProductController')->register();
    wp_route('product/edit')->action('edit@Admin\ProductController')->register();
    wp_route('product/view')->action('view@Admin\ProductController')->register();
    
    // Voucher management
    wp_route('voucher')->action('index@Admin\VoucherController')->register();
    wp_route('voucher/add')->action('add@Admin\VoucherController')->register();
    wp_route('voucher/edit')->action('edit@Admin\VoucherController')->register();
    wp_route('voucher/view')->action('view@Admin\VoucherController')->register();

    // Live chat management
    wp_route('live-chat')->action('index@Admin\LiveChatController')->register();

    // Policy management
    wp_route('policy')->action('index@Admin\PolicyController')->register();
});