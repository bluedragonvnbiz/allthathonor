<?php
/**
 * Company Introduction Section Configuration
 */

return [
    'key' => 'company_intro',
    'name' => '회사 소개 섹션',
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
                'default' => '3'
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
            ],
            [
                'key' => 'image_1',
                'type' => 'image',
                'label' => '이미지 1',
                'accept' => '.jpg,.jpeg,.png,.webp',
                'default' => '이미지.jpg'
            ],
            [
                'key' => 'image_2',
                'type' => 'image',
                'label' => '이미지 2',
                'accept' => '.jpg,.jpeg,.png,.webp',
                'default' => '이미지.jpg'
            ],
            [
                'key' => 'image_3',
                'type' => 'image',
                'label' => '이미지 3',
                'accept' => '.jpg,.jpeg,.png,.webp',
                'default' => '이미지.jpg'
            ],
            [
                'key' => 'image_4',
                'type' => 'image',
                'label' => '이미지 4',
                'accept' => '.jpg,.jpeg,.png,.webp',
                'default' => '이미지.jpg'
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
                        'default' => 'company introduction'
                    ],
                    [
                        'key' => 'main_title',
                        'type' => 'text_group',
                        'label' => '메인 타이틀',
                        'inputs' => [
                            'part1' => [
                                'placeholder' => '첫 번째 부분',
                                'default' => 'All That'
                            ],
                            'part2' => [
                                'placeholder' => '두 번째 부분',
                                'default' => 'Honors Club'
                            ]
                        ]
                    ],
                    [
                        'key' => 'description',
                        'type' => 'textarea',
                        'label' => '설명문구',
                        'default' => '올댓아너스클럽은 국내 최초의 투어 컨시어지 전문 기업으로 국내 고객과 해외 관광객을 대상으로 좀 더 편리하고 품격 높은 일등점 여행을 제공합니다.',
                        'rows' => 'rows="4"'
                    ]
                ]
            ],
            // Section 2: Our Aspiration
            [
                'fields' => [
                    [
                        'key' => 'our_aspiration_position',
                        'type' => 'html',
                        'label' => '<strong class="sub-title">좌측</strong>',
                        'is_label_html' => true
                    ],
                    [
                        'key' => 'title_1',
                        'type' => 'text',
                        'label' => '타이틀',
                        'default' => 'Our Aspiration',
                        'text_class' => 'text-uppercase'
                    ],
                    [
                        'key' => 'description_1',
                        'type' => 'textarea',
                        'label' => '설명문구',
                        'default' => '올댓아너스클럽은 고객과 함께 사회적 가치를 실현하며, 성과를 선하게 완성하는 신념받는 기업으로 성장할 것을 약속드립니다.',
                        'rows' => 'rows="3"'
                    ]
                ]
            ],
            // Section 3: Our Commitment
            [
                'fields' => [
                    [
                        'key' => 'our_commitment_position',
                        'type' => 'html',
                        'label' => '<strong class="sub-title">우측</strong>',
                        'is_label_html' => true
                    ],
                    [
                        'key' => 'title_2',
                        'type' => 'text',
                        'label' => '타이틀',
                        'default' => 'Our Commitment',
                        'text_class' => 'text-uppercase'
                    ],
                    [
                        'key' => 'description_2',
                        'type' => 'textarea',
                        'label' => '설명문구',
                        'default' => '대한민국 최고 수준의 항공사·호텔리어 출신 전문가들이 퍼스트 클래스의 품격과 세심함으로, 고객의 건강과 활력, 생활의 편리함을 더해드립니다.',
                        'rows' => 'rows="3"'
                    ]
                ]
            ]
        ]
    ]
];