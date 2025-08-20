<?php

return [
    'product_content' => [
        'title' => '상품 관리 > 새 상품 추가',
        'two_columns' => true,
        'fields' => [
            ['key' => 'exposure_status', 'type' => 'radio', 'label' => '노출상태', 'options' => ['expose' => '노출', 'not_expose' => '미노출'], 'default' => 'expose'],
            ['key' => 'main_image', 'type' => 'image', 'label' => '메인 이미지', 'accept' => '.jpg,.jpeg,.png,.webp'],
            ['key' => 'product_name', 'type' => 'text', 'label' => '상품명', 'placeholder' => '내용을 입력해주세요.'],
            ['key' => 'product_name_en', 'type' => 'text', 'label' => '상품명(영문)', 'placeholder' => '내용을 입력해주세요.'],
            ['key' => 'summary_description', 'type' => 'textarea', 'label' => '요약 설명', 'placeholder' => '내용을 입력해주세요.', 'full_width' => true, 'rows' => 4],
            ['key' => 'detailed_description', 'type' => 'editor', 'label' => '상세 설명', 'placeholder' => '내용을 입력해주세요.', 'full_width' => true],
        ]
    ]
];
