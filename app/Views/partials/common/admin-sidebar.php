<?php
/**
 * Sidebar Partial
 * Admin sidebar navigation
 */

// Định nghĩa menu structure
$adminMenus = [
    'operations' => [
        'title' => '운영 및 문의',
        'items' => [
            [
                'url' => '/management/',
                'icon' => 'icon-admin-menu-1.svg',
                'text' => '웹사이트 관리',
                'active_patterns' => ['/management/', '/management']
            ],
            [
                'url' => '#',
                'icon' => 'icon-admin-menu-2.svg',
                'text' => '1:1 문의',
                'active_patterns' => ['/inquiry', '/contact']
            ],
            [
                'url' => '#',
                'icon' => 'icon-admin-menu-3.svg',
                'text' => '약관 관리',
                'active_patterns' => ['/terms', '/policy']
            ]
        ]
    ],
    'products' => [
        'title' => '상품',
        'items' => [
            [
                'url' => '/product',
                'icon' => 'icon-admin-menu-4.svg',
                'text' => '상품 관리',
                'active_patterns' => ['/product', '/product/']
            ]
        ]
    ],
    'membership' => [
        'title' => '멤버십',
        'items' => [
            [
                'url' => '#',
                'icon' => 'icon-admin-menu-5.svg',
                'text' => '멤버십 관리',
                'active_patterns' => ['/membership', '/membership/']
            ],
            [
                'url' => '/voucher',
                'icon' => 'icon-admin-menu-6.svg',
                'text' => '혜택/바우처 관리',
                'active_patterns' => ['/voucher', '/benefits']
            ]
        ]
    ]
];

// Lấy current URL
$currentUrl = $_SERVER['REQUEST_URI'] ?? '/';

// Function kiểm tra menu active
function isMenuActive($patterns, $currentUrl) {
    foreach ($patterns as $pattern) {
        if (strpos($currentUrl, $pattern) === 0) {
            return true;
        }
    }
    return false;
}
?>

<div class="box position-sticky top-0">
    <div class="logo p-3">
        <img src="<?= THEME_URL."/assets/images/logo-admin.svg" ?>" alt="All that Honors Club">
    </div>
    
    <?php foreach ($adminMenus as $sectionKey => $section): ?>
        <div class="description">
            <?= htmlspecialchars($section['title']) ?>
        </div>	
        <ul class="list-unstyled mb-0">
            <?php foreach ($section['items'] as $item): ?>
                <?php 
                $isActive = isMenuActive($item['active_patterns'], $currentUrl);
                $activeClass = $isActive ? 'active' : '';
                ?>
                <li>
                    <a href="<?= htmlspecialchars($item['url']) ?>" class="<?= $activeClass ?>">
                        <img src="<?= THEME_URL."/assets/images/icons/".$item['icon'] ?>" alt="<?= htmlspecialchars($item['text']) ?>">
                        <span class="text-truncate"><?= htmlspecialchars($item['text']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
</div>