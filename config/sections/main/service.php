<?php
/**
 * Service Section Configuration
 */
use App\Services\ProductService;

$productService = new ProductService();

return [
    'key' => 'service',
    'name' => '서비스 섹션',
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
                'default' => '4'
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
            // Section 1: Top phrase và main title
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
                        'default' => 'OUR SERVICES'
                    ]
                ]
            ],
            // Section 2: Service 1 (Left)
            [
                'fields' => [
                    [
                        'key' => 'service_1_position',
                        'type' => 'html',
                        'label' => '<strong class="sub-title">좌측</strong>',
                        'is_label_html' => true
                    ],
                    [
                        'key' => 'service_1_code',
                        'type' => 'select',
                        'label' => '코드번호',
                        'options' => $productService->getProductOptions(),
                        'default' => 'pt0000001'
                    ],
                    [
                        'key' => 'service_1_title',
                        'type' => 'text',
                        'label' => '타이틀',
                        'text_class' => 'text-uppercase',
                        'default' => 'Gala Dining'
                    ],
                    [
                        'key' => 'service_1_description',
                        'type' => 'textarea',
                        'label' => '설명문구',
                        'default' => 'Praesent enim libero, blandit vel sapien vitae, condimentum ultricies magna et.',
                        'rows' => 'rows="3"'
                    ]
                ]
            ],
            // Section 3: Service 2 (Center)
            [
                'fields' => [
                    [
                        'key' => 'service_2_position',
                        'type' => 'html',
                        'label' => '<strong class="sub-title">중앙</strong>',
                        'is_label_html' => true
                    ],
                    [
                        'key' => 'service_2_code',
                        'type' => 'select',
                        'label' => '코드번호',
                        'options' => $productService->getProductOptions(),
                        'default' => 'pt0000001'
                    ],
                    [
                        'key' => 'service_2_title',
                        'type' => 'text',
                        'label' => '타이틀',
                        'text_class' => 'text-uppercase',
                        'default' => 'Gala Dining'
                    ],
                    [
                        'key' => 'service_2_description',
                        'type' => 'textarea',
                        'label' => '설명문구',
                        'default' => 'Praesent enim libero, blandit vel sapien vitae, condimentum ultricies magna et.',
                        'rows' => 'rows="3"'
                    ]
                ]
            ],
            // Section 4: Service 3 (Right)
            [
                'fields' => [
                    [
                        'key' => 'service_3_position',
                        'type' => 'html',
                        'label' => '<strong class="sub-title">우측</strong>',
                        'is_label_html' => true
                    ],
                    [
                        'key' => 'service_3_code',
                        'type' => 'select',
                        'label' => '코드번호',
                        'options' => $productService->getProductOptions(),
                        'default' => 'pt0000001'
                    ],
                    [
                        'key' => 'service_3_title',
                        'type' => 'text',
                        'label' => '타이틀',
                        'text_class' => 'text-uppercase',
                        'default' => 'Gala Dining'
                    ],
                    [
                        'key' => 'service_3_description',
                        'type' => 'textarea',
                        'label' => '설명문구',
                        'default' => 'Praesent enim libero, blandit vel sapien vitae, condimentum ultricies magna et.',
                        'rows' => 'rows="3"'
                    ]
                ]
            ]
        ]
    ]
];