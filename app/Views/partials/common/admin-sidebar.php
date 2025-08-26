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
                'url' => '/admin/section/',
                'icon' => 'icon-admin-menu-section.svg',
                'text' => '웹사이트 관리',
                'active_patterns' => ['/admin/section/', '/admin/section']
            ],
            [
                'url' => '/admin/inquiry',
                'icon' => 'icon-admin-menu-inquiry.svg',
                'text' => '1:1 문의',
                'active_patterns' => ['/admin/inquiry', '/admin/inquiry/']
            ],
            [
                'url' => '/admin/live-chat',
                'icon' => 'icon-admin-menu-live-chat.svg',
                'text' => '실시간 채팅',
                'active_patterns' => ['/admin/live-chat', '/admin/live-chat/']
            ],
            [
                'url' => '/admin/policy',
                'icon' => 'icon-admin-menu-policy.svg',
                'text' => '약관 관리',
                'active_patterns' => ['/admin/policy', '/admin/policy/']
            ]
            
        ]
    ],
    'products' => [
        'title' => '상품',
        'items' => [
            [
                'url' => '/admin/product',
                'icon' => 'icon-admin-menu-product.svg',
                'text' => '상품 관리',
                'active_patterns' => ['/admin/product', '/admin/product/']
            ]
        ]
    ],
    'membership' => [
        'title' => '멤버십',
        'items' => [
            [
                'url' => '/admin/membership',
                'icon' => 'icon-admin-menu-membership.svg',
                'text' => '멤버십 관리',
                'active_patterns' => ['/admin/membership', '/admin/membership/']
            ],
            [
                'url' => '/admin/voucher',
                'icon' => 'icon-admin-menu-voucher.svg',
                'text' => '혜택/바우처 관리',
                'active_patterns' => ['/admin/voucher', '/admin/benefits']
            ]
        ]
    ]
];

// Get current URL
$currentUrl = $_SERVER['REQUEST_URI'] ?? '/';

// Function find active menu item
function findActiveMenuItem($adminMenus, $currentUrl) {
    $bestMatch = null;
    $bestMatchLength = 0;
    
    foreach ($adminMenus as $sectionKey => $section) {
        foreach ($section['items'] as $itemIndex => $item) {
            foreach ($item['active_patterns'] as $pattern) {
                // Check exact match
                if ($currentUrl === $pattern) {
                    if (strlen($pattern) > $bestMatchLength) {
                        $bestMatch = ['section' => $sectionKey, 'item' => $itemIndex];
                        $bestMatchLength = strlen($pattern);
                    }
                }
                // Check prefix match (currentUrl begin with pattern + /)
                elseif (strpos($currentUrl, $pattern . '/') === 0) {
                    if (strlen($pattern) > $bestMatchLength) {
                        $bestMatch = ['section' => $sectionKey, 'item' => $itemIndex];
                        $bestMatchLength = strlen($pattern);
                    }
                }
            }
        }
    }
    
    return $bestMatch;
}

// Find active menu item
$activeMenuItem = findActiveMenuItem($adminMenus, $currentUrl);
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
            <?php foreach ($section['items'] as $itemIndex => $item): ?>
                <?php 
                // Check if item is active
                $isActive = ($activeMenuItem && 
                            $activeMenuItem['section'] === $sectionKey && 
                            $activeMenuItem['item'] === $itemIndex);
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