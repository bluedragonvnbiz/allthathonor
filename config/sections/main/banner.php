<?php
/**
 * Banner Section Configuration
 */

return [
    'key' => 'banner',
    'name' => '콘텐츠 상세',
    'section_info' => [
        'title' => '섹션 정보',
        'two_columns' => true,
        'fields' => [
            [
                'key' => 'location',
                'type' => 'display_text',
                'label' => '위치',
                'default' => '메인',
            ],
            [
                'key' => 'section_number',
                'type' => 'display_text',
                'label' => '섹션',
                'default' => '1'
            ],
            [
                'key' => 'exposure_status',
                'type' => 'radio',
                'label' => '노출상태',
                'options' => [
                    'expose' => '노출',
                    'not_expose' => '미노출'
                ],
                'default' => 'expose'
            ],
            [
                'key' => 'background_image',
                'type' => 'image',
                'label' => '배경이미지',
                'accept' => '.jpg,.jpeg,.png,.webp'
            ]
        ]
    ],
    'content_info' => [
        'title' => '내용 정보',
        'fields' => [
            [
                'key' => 'top_phrase',
                'type' => 'text',
                'label' => '상단 문구',
                'default' => 'premium service, tailored for you'
            ],
            [
                'key' => 'main_title',
                'type' => 'text',
                'label' => '메인 타이틀',
                'default' => 'ALL THAT HONORS CLUB'
            ],
            [
                'key' => 'description',
                'type' => 'textarea',
                'label' => '설명 문구',
                'default' => '대한민국 최고 항공사와 호텔 출신 전문가들이 오직 당신만을 위한 퍼스트클래스 품격을 완성합니다.'
            ]
        ]
    ]
];
