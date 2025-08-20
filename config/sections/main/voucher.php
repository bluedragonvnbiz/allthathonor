<?php
/**
 * Voucher/Benefit Section Configuration
 */

return [
    'key' => 'voucher',
    'name' => '바우처/혜택 섹션',
    'section_info' => [
        'title' => '섹션 정보',
        'two_columns' => true,
        'fields' => [
            [
                'key' => 'location',
                'type' => 'display_text',
                'label' => '위치',
                'default' => '메인'
            ],
            [
                'key' => 'section_number',
                'type' => 'display_text',
                'label' => '섹션',
                'default' => '5'
            ],
            [
                'key' => 'exposure_status',
                'type' => 'radio',
                'label' => '노출상태',
                'full_width' => true,
                'options' => [
                    'expose' => '노출',
                    'not_expose' => '미노출'
                ],
                'default' => 'expose'
            ]
        ]
    ],
    'content_info' => [
        'title' => '내용 정보',
        'sections' => [
            // Section 1: Header
            [
                'fields' => [
                    [
                        'key' => 'top_phrase',
                        'type' => 'text',
                        'label' => '상단 문구',
                        'default' => 'our service'
                    ],
                    [
                        'key' => 'main_title',
                        'type' => 'text',
                        'label' => '메인 타이틀',
                        'default' => 'BENEFIT/VOUCHER'
                    ]
                ]
            ],
            // Section 2: Voucher 1
            [
                'fields' => [
                    [
                        'key' => 'voucher_1_group',
                        'type' => 'field_group',
                        'fields' => [
                            [
                                'key' => 'voucher_1_category',
                                'type' => 'display_text',
                                'label' => '카테고리',
                                'default' => 'Voucher'
                            ],
                            [
                                'key' => 'voucher_1_image',
                                'type' => 'image',
                                'label' => '이미지',
                                'accept' => '.jpg,.jpeg,.png,.webp',
                                'default' => '이미지.jpg'
                            ]
                        ]
                    ],
                    [
                        'key' => 'voucher_1_title',
                        'type' => 'text',
                        'label' => '연결 혜택/바우처',
                        'full_width' => true,
                        'default' => 'Healthcare and DNA'
                    ]
                ]
            ],
            // Section 3: Voucher 2
            [
                'fields' => [
                    [
                        'key' => 'voucher_2_group',
                        'type' => 'field_group',
                        'fields' => [
                            [
                                'key' => 'voucher_2_category',
                                'type' => 'display_text',
                                'label' => '카테고리',
                                'default' => 'event invitation'
                            ],
                            [
                                'key' => 'voucher_2_image',
                                'type' => 'image',
                                'label' => '이미지',
                                'accept' => '.jpg,.jpeg,.png,.webp',
                                'default' => '이미지.jpg'
                            ]
                        ]
                    ],
                    [
                        'key' => 'voucher_2_title',
                        'type' => 'text',
                        'label' => '연결 혜택/바우처',
                        'full_width' => true,
                        'default' => 'VIP INVITATION'
                    ]
                ]
            ]
        ]
    ]
];