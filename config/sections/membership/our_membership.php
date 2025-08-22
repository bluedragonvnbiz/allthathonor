<?php

return [
    'key' => 'our_membership',
    'name' => '멤버십',
    
    'section_info' => [
        'title' => '섹션 정보',
        'two_columns' => 1,
        'fields' => [
            [
                'key' => 'location',
                'type' => 'display_text',
                'label' => '위치',
                'default' => '멤버십'
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
                'default' => 'Our Service'
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
                'default' => 'The Heritage Travel ClubThe Heritage Travel ClubThe Heritage Travel Club',
                'rows' => 4
            ]
        ]
    ]
];