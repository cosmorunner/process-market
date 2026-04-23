<?php

return [
    'internal' => [
        'action_type_component' => [
            [
                'namespace' => 'allisa',
                'identifier' => 'form',
                'name' => 'Formular - allisa/form',
                'icon' => 'feed',
                'default_options' => [
                    'sets' => [
                        [
                            'label' => '',
                            'sort' => 0,
                            'width' => '12',
                            'fields' => [],
                        ]
                    ],
                    'display' => [
                        'hidden' => []
                    ]
                ]
            ],
            [
                'namespace' => 'allisa',
                'identifier' => 'collection',
                'name' => 'Liste - allisa/collection',
                'icon' => 'view_stream',
                'default_options' => [
                    'list_config_id' => '',
                    'mapping' => json_decode('{}'),
                    'binding' => json_decode('{}'),
                    'hide_when_empty' => false
                ]
            ],
            [
                'namespace' => 'allisa',
                'identifier' => 'progress-bar',
                'name' => 'Fortschrittsbalken - allisa/progress-bar',
                'icon' => 'trending_up',
                'default_options' => [
                    'type' => 'progress_bar',
                    'label' => '',
                    'icon' => 'star',
                    'value' => '50',
                    'min' => '0',
                    'max' => '100',
                    'color' => '#013370',
                    'helper_text' => '',
                    'show_value' => true
                ]
            ],
            [
                'namespace' => 'allisa',
                'identifier' => 'file-preview',
                'name' => 'Datei-Vorschau - allisa/file-preview',
                'icon' => 'image',
                'default_options' => [
                    'value' => '',
                    'show_download' => true,
                    'show_empty' => true,
                    'css_max_height' => ''
                ]
            ]
        ]
    ]
];