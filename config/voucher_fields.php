<?php

// Load membership options dynamically
$membershipOptions = [];
if (class_exists('\App\Services\MembershipService')) {
    $membershipService = new \App\Services\MembershipService();
    $availableGrades = $membershipService->getAvailableGrades();
    
    foreach ($availableGrades as $grade) {
        $membershipOptions[$grade->grade_id] = $grade->grade_name;
    }
}

// Fallback options if no memberships found
if (empty($membershipOptions)) {
    $membershipOptions = ['1' => 'PRIME', '2' => 'SIGNATURE'];
}

return [
    'one_submit' => true,
    'section_info' => [
        'title' => '혜택/바우처 정보',
        'fields' => [
            ['key' => 'category', 'type' => 'checkbox', 'label' => '등급', 'options' => $membershipOptions],
            ['key' => 'status', 'type' => 'radio', 'label' => '상태', 'options' => ['expose' => '노출', 'not_expose' => '미노출'], 'default' => 'expose'],
            ['key' => 'type', 'type' => 'checkbox', 'label' => '유형', 'options' => ['voucher' => 'VOUCHER', 'event_invitation' => 'EVENT INVITATION']],
            ['key' => 'voucher_name', 'type' => 'display_text', 'label' => '', 'placeholder' => ''],
            ['key' => 'image', 'type' => 'image', 'label' => '대표이미지', 'accept' => '.jpg,.jpeg,.png,.webp', 'placeholder' => '파일을 선택해주세요.'],
        ]
    ],
    'content_info' => [
        'title' => '혜택/바우처 내용',
        'sections' => [
            [
                'fields' => [
                    ['key' => 'name', 'type' => 'text', 'label' => '혜택/바우처명', 'placeholder' => '내용을 입력해주세요.'],
                ]
            ],
            [
                'fields' => [
                    ['key' => 'short_description', 'type' => 'textarea', 'label' => '요약 설명', 'placeholder' => '내용을 입력해주세요.', 'rows' => 4],
                ]
            ],
            [
                'fields' => [
                    ['key' => 'detail_description', 'type' => 'editor', 'label' => '상세 설명', 'placeholder' => '내용을 입력해주세요.', 'full_width' => true],
                ]
            ],
        ]
    ]
];
