<?php
/**
 * Membership Section Configuration
 */

return [
    'key' => 'membership',
    'name' => '멤버십 섹션',
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
                'default' => '2'
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
        ]
    ],
    'content_info' => [
        'title' => '내용 정보',
        'fields' => [
            [
                'key' => 'top_phrase',
                'type' => 'text',
                'label' => '상단 문구',
                'default' => 'our membership'
            ],
            [
                'key' => 'main_title',
                'type' => 'text',
                'label' => '메인 타이틀',
                'default' => 'The Heritage Travel Club'
            ],
            [
                'key' => 'description',
                'type' => 'textarea',
                'label' => '설명 문구',
                'default' => '올댓아너스의 Excutive Travel Care Annual Membership은 국내 최고 항공사·호텔 출신 전문가들이 선사하는 퍼스트클래스 맞춤형 서비스를 위한 프라이빗 멤버십입니다. 단순한 혜택을 넘어, 당신만을 위한 하이엔드 서비스로 완성됩니다.',
                'rows' => 'rows="4"'
            ]
        ]
    ]
];
