<?php

return [
    'section_info' => [
        'title' => '혜택/바우처 정보',
        'sections' => [
            [
                'fields' => [
                    ['key' => 'membership_name', 'type' => 'text', 'label' => '멤버십명', 'placeholder' => ''],
                    ['key' => 'top_phrase', 'type' => 'text', 'label' => '상단 문구', 'placeholder' => ''],
                    ['key' => 'status', 'type' => 'radio', 'label' => '노출상태', 'options' => ['expose' => '노출', 'not_expose' => '미노출'], 'default' => 'expose'],
                    ['key' => 'image', 'type' => 'image', 'label' => '이미지', 'accept' => '.jpg,.jpeg,.png,.webp'],
                ]
            ],
            [
                'fields' => [
                    ['key' => 'sale_price', 'type' => 'text', 'label' => '판매가격 (원)', 'placeholder' => ''],
                ]
            ],
            [
                'fields' => [
                    ['key' => 'summary_description', 'type' => 'textarea', 'label' => '요약 설명', 'placeholder' => '', 'full_width' => true, 'rows' => 3],
                ]
            ],
            [
                'fields' => [
                    ['key' => 'notes', 'type' => 'textarea', 'label' => '유의사항', 'placeholder' => '', 'full_width' => true, 'rows' => 10],
                ]
            ]
        ]
        
    ]
];
