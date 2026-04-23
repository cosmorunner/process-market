<?php
/**
 * Cytoscape Graphen Konfiguration.
 */

use App\ProcessType\ActionRule;

return [
    'empty' => [
        'name' => 'Leerer Prozess',
        'namespace' => 'allisa',
        'identifier' => 'empty',
        'image' => 'star',
        'version' => 'develop',
        'definition_version' => '1.0.0',
        'description' => '',
        'reference_pattern' => '',
        'default_role_id' => 'f33452ff-ad76-4eb7-b137-57cf4f16c6f9',
        'public_role_id' => 'f33452ff-ad76-4eb7-b137-57cf4f16c6f9',
        'history_list_config_slug' => null,
        'published_at' => null,
        'categories' => [
            [
                'id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
                'name' => 'Workflow',
                'description' => 'Prozess-Fortschritt bearbeiten.',
                'image' => 'double_arrow',
                'color' => '#72c6ff',
                'sort' => 0,
                'locked' => true,
                'hidden' => false
            ],
            [
                'id' => 'a5dad74e-a515-434b-ad96-04f22748b68d',
                'name' => 'Versteckt',
                'description' => 'Aktionen dieser Kategorie werden in der Web-Oberfläche nicht angezeigt, können aber durch Prozessoren oder einem REST-API Aufruf ausgeführt werden.',
                'image' => 'visibility_off',
                'color' => '#72c6ff',
                'sort' => 0,
                'locked' => true,
                'hidden' => true
            ]
        ],
        'outputs' => [],
        'status_types' => [],
        'action_types' => [],
        'roles' => [
            [
                'id' => 'f33452ff-ad76-4eb7-b137-57cf4f16c6f9',
                'name' => 'Maintainer',
                'description' => 'Vollständigen Zugriff auf alle Daten, Aktionen, Status und Artefakte.',
                'active' => true,
                'permissions' => [
                    // Rechte bezüglich Prozess-Instanz.
                    [
                        'id' => '8a31d654-2fd7-443c-a22f-da3ffe68ad17',
                        'name' => 'Alle Prozess-Daten einsehen',
                        'description' => 'Einsehen der Prozessdaten.',
                        'ident' => 'process_type.process.view_data',
                        'conditions' => [],
                    ],
                    [
                        'id' => '50696c9c-edfa-4dd7-adbd-7dac00a73f4c',
                        'name' => 'Alle Aktionen ausführen',
                        'description' => 'Ausführen aller Aktionen des Prozesses.',
                        'ident' => 'process_type.process.execute_actions',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'b12a9b1a-042d-4e4f-9725-7dc841d22b8b',
                        'name' => 'Alle Aktionen einsehen',
                        'description' => 'Alle Aktionen einsehen.',
                        'ident' => 'process_type.process.view_actions',
                        'conditions' => [],
                    ],
                    [
                        'id' => '9820a107-7265-4c00-86b0-18a0c068d2b8',
                        'name' => 'Alle Aktions-Ausführungen einsehen',
                        'description' => 'Alle Aktions-Ausführungen einsehen.',
                        'ident' => 'process_type.process.view_action_executions',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'e27f2246-86dd-4e61-80f9-551a20553c4d',
                        'name' => 'Alle Aktionen exportieren',
                        'description' => 'Alle Aktionen exportieren.',
                        'ident' => 'process_type.process.export_actions',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'd68f44be-aedd-4765-b074-8806bcf38a22',
                        'name' => 'Alle Status einsehen',
                        'description' => 'Einsehen der aktuellen Prozess-Situation.',
                        'ident' => 'process_type.process.view_situation',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'c62c0ab0-516c-4a7a-a80c-8f4e264a6b24',
                        'name' => 'Alle Listen einsehen',
                        'description' => 'Prozess-Listen mit deren Inhalten einsehen.',
                        'ident' => 'process_type.process.view_lists',
                        'conditions' => [],
                    ],
                    [
                        'id' => '27539833-035f-47bc-805d-2daf655ee4d6',
                        'name' => 'Aktionen rückgängig machen',
                        'description' => 'Die Ausführung einer Aktion rückgängig machen. Der Prozess wird auf die Situation vor der Aktionsausführung gesetzt.',
                        'ident' => 'process_type.process.revert_actions',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'f1d2e3f1-b260-47e4-93e6-4eda3dc0085d',
                        'name' => 'Verlauf einsehen',
                        'description' => 'Aktionsverlauf und Situationsveränderungen einsehen.',
                        'ident' => 'process_type.process.view_history',
                        'conditions' => [],
                    ],
                    [
                        'id' => '6ea60b36-b2b3-415f-9cc6-41e1cfdfce12',
                        'name' => 'Alle Menü-Einträge einsehen',
                        'description' => 'Alle Menü-Einträge einsehen.',
                        'ident' => 'process_type.process.view_menu_items',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'e74e17d4-d740-40bc-85a6-c86e86d4f8cf',
                        'name' => 'Artefakte einsehen',
                        'description' => 'Einsehen der erzeugten Dokumente und E-Mails.',
                        'ident' => 'process_type.process.view_artifacts',
                        'conditions' => [],
                    ]
                ]
            ]
        ],
        'list_configs' => [
            [
                'id' => '9aa5c5ce-75cc-40fe-875e-46f6e8b98120',
                'name' => 'Alle Prozesse',
                'slug' => 'all',
                'description' => 'Alle Prozesse dieser Allisa Plattform.',
                'template' => 'processes',
                'data' => [
                    'limit_to_user_involvement' => false,
                    'rows_per_page' => 10,
                    'header_button' => [
                        'label' => 'app.new_process',
                        'type' => 'link',
                        'type_options' => [
                            'url' => '/processes/create',
                            'bindings' => [],
                            'target' => '_self',
                        ]
                    ],
                    'enable_search' => true,
                    'enable_pagination' => true,
                    'enable_total_count' => true,
                    'enable_label' => true,
                    'quick_filter' => [],
                    'source_type' => 'sql',
                    'source' => [
                        'from' => 'processes',
                        'select' => [
                            'processes.id as processes_id',
                            'CONCAT(\'process|\', processes.id) as processes_pipe_notation',
                            'processes.name as processes_name',
                            'processes.image as processes_image',
                            'processes.created_at as processes_created_at',
                            'process_type_metas.name as process_type_metas_name',
                            'process_type_metas.image as process_type_metas_image',
                            'process_types.version as process_types_version'
                        ],
                        'join' => [
                            [
                                'process_type_metas',
                                'process_type_metas.id',
                                '=',
                                'processes.process_type_meta_id'
                            ],
                            [
                                'process_types',
                                'process_types.id',
                                '=',
                                'processes.process_type_id'
                            ]
                        ],
                        'orderBy' => [
                            [
                                'processes_created_at',
                                'desc'
                            ]
                        ],
                    ],
                    'columns' => [
                        [
                            'type' => 'icon',
                            'data' => 'processes_image',
                            'sort' => 0,
                            'alias' => 'process_type_metas_image',
                            'type_options' => [
                                'mapping' => [],
                                'tooltip' => '',
                                'hide' => [],
                                'size' => 'normal',
                                'colors' => [
                                    'secondary' => ['*'],
                                    'warning' => ['bug_report'],
                                    'primary' => ['star'],
                                ]
                            ],
                            'label' => '',
                            'width' => 1,
                            'searchable' => false,
                            'sortable' => false,
                            'color' => ''
                        ],
                        [
                            'type' => 'url',
                            'data' => 'processes_name',
                            'sort' => 1,
                            'type_options' => [
                                'url' => '/processes/$',
                                'bindings' => [
                                    'processes_id'
                                ],
                                'target' => '_self',
                            ],
                            'label' => 'Name',
                            'width' => 4,
                            'searchable' => true,
                            'sortable' => true,
                            'color' => ''
                        ],
                        [
                            'type' => 'text',
                            'data' => 'process_type_metas_name',
                            'sort' => 2,
                            'type_options' => [],
                            'label' => 'Prozesstyp-Name',
                            'width' => 3,
                            'searchable' => true,
                            'sortable' => true,
                            'color' => ''
                        ],
                        [
                            'type' => 'text',
                            'data' => 'processes_created_at',
                            'sort' => 3,
                            'type_options' => [
                                'date_format' => 'd.m.Y H:i'
                            ],
                            'label' => 'Erstelldatum',
                            'width' => 2,
                            'searchable' => true,
                            'sortable' => true,
                            'color' => ''
                        ],
                        [
                            'type' => 'text',
                            'data' => 'process_types_version',
                            'sort' => 4,
                            'type_options' => [],
                            'label' => 'Version',
                            'width' => 2,
                            'searchable' => true,
                            'sortable' => false,
                            'color' => ''
                        ]
                    ],
                ]
            ],
            [
                'id' => '77ebf823-a06a-494b-8bf1-5864c249cc98',
                'name' => 'Prozess-Verknüpfungen',
                'slug' => 'relations',
                'description' => 'Alle Verknüpfungen des Prozesses.',
                'template' => 'process_relations',
                'data' => [
                    'limit_to_user_involvement' => false,
                    'rows_per_page' => 10,
                    'enable_label' => true,
                    'enable_search' => true,
                    'enable_pagination' => true,
                    'enable_total_count' => true,
                    'quick_filter' => [],
                    'source_type' => 'sql',
                    'source' => [
                        'from' => 'processes',
                        'select' => [
                            'CONCAT(\'process|\', processes.id) as processes_pipe_notation',
                            'processes.id as processes_id',
                            'processes.name as processes_name',
                            'relations.relation_type_name as relations_relation_type_name',
                            'relations.created_at as relations_created_at',
                            'relations.reference as relations_reference'
                        ],
                        'join' => [
                            [
                                'table' => 'relations',
                                'on' => [
                                    [
                                        'processes.id',
                                        '=',
                                        'relations.left'
                                    ],
                                ],
                                'orOn' => [
                                    [
                                        'processes.id',
                                        '=',
                                        'relations.right'
                                    ],
                                ],
                            ],
                            [
                                'process_type_metas',
                                'process_type_metas.id',
                                '=',
                                'processes.process_type_meta_id'
                            ],
                            [
                                'process_types',
                                'process_types.id',
                                '=',
                                'processes.process_type_id'
                            ]
                        ],
                        'where' => [
                            [
                                'processes.id',
                                '!=',
                                '[[process.meta.id]]'
                            ],
                            [
                                [
                                    'relations.left',
                                    '=',
                                    '[[process.meta.id]]'
                                ],
                                [
                                    'relations.right',
                                    '=',
                                    '[[process.meta.id]]'
                                ]
                            ]
                        ],
                        'orderBy' => [
                            [
                                'relations_created_at',
                                'desc'
                            ]
                        ],
                    ],
                    'columns' => [
                        [
                            'type' => 'url',
                            'data' => 'processes_name',
                            'sort' => 0,
                            'type_options' => [
                                'url' => '/processes/$',
                                'bindings' => [
                                    'processes_id'
                                ],
                                'target' => '_self',
                            ],
                            'label' => 'Name',
                            'width' => 4,
                            'searchable' => true,
                            'sortable' => true,
                            'color' => ''
                        ],
                        [
                            'type' => 'text',
                            'data' => 'relations_relation_type_name',
                            'sort' => 1,
                            'type_options' => [],
                            'label' => 'Verknüpfungstyp',
                            'width' => 3,
                            'searchable' => true,
                            'sortable' => true,
                            'color' => ''
                        ],
                        [
                            'type' => 'text',
                            'data' => 'relations_reference',
                            'sort' => 2,
                            'type_options' => [],
                            'label' => 'Referenz',
                            'width' => 3,
                            'searchable' => true,
                            'sortable' => true,
                            'color' => ''
                        ],
                        [
                            'type' => 'text',
                            'data' => 'relations_created_at',
                            'sort' => 3,
                            'type_options' => [
                                'date_format' => 'd.m.Y H:i'
                            ],
                            'label' => 'Verknüpft am',
                            'width' => 2,
                            'searchable' => false,
                            'sortable' => true,
                            'color' => ''
                        ]
                    ],
                ]
            ]
        ],
        'menu_items' => [
            [
                'id' => 'a8778e6c-007b-4ea5-9095-f347e636cc73',
                'label' => 'Übersicht',
                'url' => '[[process.meta.url((Prozess-URL - Übersicht))]]',
                'location' => 'LOCATION_SIDEBAR',
                'route_name' => 'process.show',
                'sort' => 1,
                'image' => 'search',
                'parent_id' => null,
                'views' => ['layouts.process'],
                'target' => '_self',
                'conditions' => []
            ],
            [
                'id' => '76fd8ccc-52a4-44ff-8f7c-01e4aa476762',
                'label' => 'Daten',
                'url' => '[[process.meta.data_url((Prozess-URL - Daten))]]',
                'location' => 'LOCATION_SIDEBAR',
                'route_name' => 'process.data',
                'sort' => 2,
                'image' => 'storage',
                'parent_id' => null,
                'views' => ['layouts.process'],
                'target' => '_self',
                'conditions' => []
            ],
            [
                'id' => '90a2427a-c98b-4fc1-8cd1-1fa698d93b0b',
                'label' => 'Verknüpfungen',
                'url' => '[[process.urls.lists.relations((Prozess-Listen-URL - Prozess-Verknüpfungen))]]',
                'location' => 'LOCATION_SIDEBAR',
                'route_name' => 'process.list',
                'sort' => 3,
                'image' => 'list',
                'parent_id' => null,
                'views' => ['layouts.process'],
                'target' => '_self',
                'conditions' => []
            ]
        ],
        'templates' => [],
        'relation_types' => [],
        'events' => [],
        'listeners' => [],
        'javascript' => [],
        'dependencies' => [
            'process_types' => [],
            'action_type_plugins' => [],
            'status_type_plugins' => []
        ],
        'unique' => []
    ],
    'empty_calculated' => [],
    'actionrule_test' => [
        'name' => '',
        'namespace' => '',
        'identifier' => '',
        'image' => 'star',
        'version' => '0.0.1',
        'description' => '',
        'action_type_mappings' => [],
        'reference_pattern' => '',
        'default_role_id' => 'f33452ff-ad76-4eb7-b137-57cf4f16c6f9',
        'published_at' => null,
        'categories' => [
            [
                'id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
                'name' => 'Workflow',
                'description' => 'Prozess-Fortschritt bearbeiten.',
                'image' => 'settings',
                'color' => '#aad0ff'
            ]
        ],
        'outputs' => [],
        'status_types' => [
            [
                'id' => '61fa7120-55cb-426a-a665-70ed42e31921',
                'reference' => '61fa7120-55cb-426a-a665-70ed42e31921',
                'name' => 'Nebenstatus',
                'description' => '',
                'namespace' => 'allisa',
                'identifier' => 'simple',
                'version' => '1.0.0',
                'options' => [],
                'image' => '',
                'sort' => 0,
                'size' => '4x1',
                'default' => '0.000',
                'states' => [
                    [
                        'id' => '25edf3c7-9d33-4077-a9fe-c38d0fe2e3a3',
                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
                        'description' => 'Zustand -2.000 bis -1.001',
                        'image' => '',
                        'min' => '-2.000',
                        'max' => '-1.001',
                        'visible' => true,
                        'color' => '#B3EBFF'
                    ],
                    [
                        'id' => 'c2d4e2c9-f942-4622-8a09-87d743c13582',
                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
                        'description' => 'Zustand -1.000 bis -0.001',
                        'image' => '',
                        'min' => '-1.000',
                        'max' => '-0.001',
                        'visible' => true,
                        'color' => '#B3EBFF'
                    ],
                    [
                        'id' => '94203e73-33c0-44aa-90af-c637c04aead9',
                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
                        'description' => 'Zustand 0.000 bis 0.999',
                        'image' => '',
                        'min' => '0.000',
                        'max' => '0.999',
                        'visible' => true,
                        'color' => '#B3EBFF'
                    ],
                    [
                        'id' => '45b3d0d4-7af5-49ae-8103-8fa471f29d4f',
                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
                        'description' => 'Zustand 1.000 bis 1.999',
                        'image' => '',
                        'min' => '1.000',
                        'max' => '1.999',
                        'visible' => true,
                        'color' => '#6BFEFE'
                    ],
                    [
                        'id' => 'c841e036-3205-405c-a4d0-7c0b5265a5eb',
                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
                        'description' => 'Zustand 5.000',
                        'image' => '',
                        'min' => '5.000',
                        'max' => '5.000',
                        'visible' => true,
                        'color' => '#6BFEFE'
                    ],
                    [
                        'id' => 'c9cdf759-a970-4246-89e6-65b05461b619',
                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
                        'description' => 'Zustand 6.000',
                        'image' => '',
                        'min' => '6.000',
                        'max' => '6.000',
                        'visible' => true,
                        'color' => '#6BFEFE'
                    ]
                ]
            ]
        ],
        'action_types' => [
//            [
//                'id' => '540d87d5-3008-4d4e-b65b-141b0fd0df6d',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus > 0',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => 'dccb2e2e-cd1e-4199-a052-9a379dcc14f9',
//                        'action_type_id' => '540d87d5-3008-4d4e-b65b-141b0fd0df6d',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_GREATER,
//                        'values' => ['0.000'],
//                        'state_ids' => [],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '055b0cb8-6481-42b3-84ac-d9738b511fb0',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus >= 0',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => 'c5ddc80e-45d2-48fc-8413-2d8d4e1e90f7',
//                        'action_type_id' => '055b0cb8-6481-42b3-84ac-d9738b511fb0',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_GREATER_OR_EQUAL,
//                        'values' => ['0.000'],
//                        'state_ids' => [],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '23651a74-f9b3-48d7-b969-af7a90a9649b',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus < 0',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => '98fe3bfd-f07c-428a-9b0e-404cb00573e4',
//                        'action_type_id' => '23651a74-f9b3-48d7-b969-af7a90a9649b',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_LOWER,
//                        'values' => ['0.000'],
//                        'state_ids' => [],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '7015b711-6f71-43ec-a77d-74c1cf46db36',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus <= 0',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => 'aee12b11-c731-4e21-8559-3bef30c520e5',
//                        'action_type_id' => '7015b711-6f71-43ec-a77d-74c1cf46db36',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_LOWER_OR_EQUAL,
//                        'values' => ['0.000'],
//                        'state_ids' => [],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '93c95114-101e-4bc9-8b41-44c6a99a4aa1',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus != 0',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => 'c741b73a-5364-4112-a251-63d3554735e8',
//                        'action_type_id' => '93c95114-101e-4bc9-8b41-44c6a99a4aa1',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_NOT_IN_ARRAY,
//                        'values' => ['0.000'],
//                        'state_ids' => [],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => 'c1e3d92e-9120-42ce-b162-cb575ab24110',
//                'category_id' => 'd4d14cc0-d4a3-4f22-81e9-66169f4f70e8',
//                'name' => 'Aktionsregel: Wenn Nebenstatus nicht 5 oder 6',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => 'b046ab16-2b7d-406b-b55b-b8e27a1e6028',
//                        'action_type_id' => 'c1e3d92e-9120-42ce-b162-cb575ab24110',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_NOT_IN_ARRAY,
//                        'values' => ['5.000', '6.000'],
//                        'state_ids' => [],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '187649cb-a8d2-4e85-920a-3fab7bd74955',
//                'category_id' => 'd4d14cc0-d4a3-4f22-81e9-66169f4f70e8',
//                'name' => 'Aktionsregel: Wenn Nebenstatus nicht 0 oder 1',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => '15c63260-c9bb-4339-8f29-ecee4fe1a62f',
//                        'action_type_id' => '187649cb-a8d2-4e85-920a-3fab7bd74955',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_NOT_IN_ARRAY,
//                        'values' => ['0.000', '1.000'],
//                        'state_ids' => [],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '0ce85d86-8814-4736-9722-312e1e6715db',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus in (0, 10, 20)',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => 'dda8b176-8a7d-49c0-92a8-eec1d9900d82',
//                        'action_type_id' => '0ce85d86-8814-4736-9722-312e1e6715db',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_IN_ARRAY,
//                        'values' => ['0.000', '10.000', '20.000'],
//                        'state_ids' => [],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '55dca4ff-9599-4966-b9f8-4eb3c430f63f',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus zwischen -5 und 5',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => 'b16ebd06-90aa-4ba4-99f2-ea717fa82240',
//                        'action_type_id' => '55dca4ff-9599-4966-b9f8-4eb3c430f63f',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_IN_BETWEEN,
//                        'values' => ['-5.000', '5.000'],
//                        'state_ids' => [],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => 'cf6ddce9-4aea-4ea4-9b06-106e8803dd2a',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus = "Zustand 1.000 bis 1.999"',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => '24cccf67-bcb5-418d-8b16-8f134371b533',
//                        'action_type_id' => 'cf6ddce9-4aea-4ea4-9b06-106e8803dd2a',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_IN_ARRAY,
//                        'values' => [],
//                        'state_ids' => ['45b3d0d4-7af5-49ae-8103-8fa471f29d4f'],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '5f4e6644-1d12-474f-b4b8-4290aa12e20a',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus != "Zustand 1.000 bis 1.999"',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => '05f97313-7f71-4a52-843a-241adaa6df4a',
//                        'action_type_id' => '5f4e6644-1d12-474f-b4b8-4290aa12e20a',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_NOT_IN_ARRAY,
//                        'values' => [],
//                        'state_ids' => ['45b3d0d4-7af5-49ae-8103-8fa471f29d4f'],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => 'c78965ea-def9-4a22-a4ce-912fbee24249',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus < "Zustand -1.000 bis -0.001"',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => 'e063d5a6-8e6a-4ef3-90ab-14a1c1238268',
//                        'action_type_id' => 'c78965ea-def9-4a22-a4ce-912fbee24249',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_LOWER,
//                        'values' => [],
//                        'state_ids' => ['c2d4e2c9-f942-4622-8a09-87d743c13582'],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '952e6974-1260-4fea-aa5a-31eb0562f7d2',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus <= "Zustand -1.000 bis -0.001"',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => 'fc799e8a-c989-49c5-86ed-3538b82c3a5b',
//                        'action_type_id' => '952e6974-1260-4fea-aa5a-31eb0562f7d2',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_LOWER_OR_EQUAL,
//                        'values' => [],
//                        'state_ids' => ['c2d4e2c9-f942-4622-8a09-87d743c13582'],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '1861936d-4066-4fd9-8e05-d5159a68ad01',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus > "Zustand -1.000 bis -0.001"',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => '202a64e9-2498-481f-849f-14d70b9c2caf',
//                        'action_type_id' => '1861936d-4066-4fd9-8e05-d5159a68ad01',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_GREATER,
//                        'values' => [],
//                        'state_ids' => ['c2d4e2c9-f942-4622-8a09-87d743c13582'],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => 'eb6cff4b-aa75-409c-a1e5-3cf6258aa1c3',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus >= "Zustand -1.000 bis -0.001"',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => '46f2e32f-1b23-4324-9f83-4087ad3486db',
//                        'action_type_id' => 'eb6cff4b-aa75-409c-a1e5-3cf6258aa1c3',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_GREATER_OR_EQUAL,
//                        'values' => [],
//                        'state_ids' => ['c2d4e2c9-f942-4622-8a09-87d743c13582'],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '0f640741-17d1-495f-83cc-4baa97781a01',
//                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
//                'name' => 'Aktionsregel: Wenn Nebenstatus entweder "Zustand -1.000 bis -0.001" oder "Zustand 1.000 bis 1.999"',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => 'dbd1b920-ae3a-48b5-9167-82bdf90360eb',
//                        'action_type_id' => '0f640741-17d1-495f-83cc-4baa97781a01',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_IN_ARRAY,
//                        'values' => [],
//                        'state_ids' => ['c2d4e2c9-f942-4622-8a09-87d743c13582', '45b3d0d4-7af5-49ae-8103-8fa471f29d4f'],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
//            [
//                'id' => '9c207ca1-a3e5-4bbc-ba78-4f01b0251e9a',
//                'category_id' => 'd4d14cc0-d4a3-4f22-81e9-66169f4f70e8',
//                'name' => 'Aktionsregel: Wenn Nebenstatus ist nicht "Zustand -1.000 bis -0.001" oder "Zustand 1.000 bis 1.999"',
//                'description' => '',
//                'image' => 'flash_on',
//                'visibility' => 1,
//                'sort' => 0,
//                'inputs' => [],
//                'outputs' => [],
//                'action_rules' => [
//                    [
//                        'id' => '7a7b3242-4d2a-4f35-abfc-6f2773b546cc',
//                        'action_type_id' => '9c207ca1-a3e5-4bbc-ba78-4f01b0251e9a',
//                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
//                        'type' => ActionRule::TYPE_STATUS,
//                        'operator' => ActionRule::OPERATOR_NOT_IN_ARRAY,
//                        'values' => [],
//                        'state_ids' => ['c2d4e2c9-f942-4622-8a09-87d743c13582', '45b3d0d4-7af5-49ae-8103-8fa471f29d4f'],
//                        'group' => 'default'
//                    ],
//                ],
//                'status_rules' => [],
//                'processors' => [],
//                'components' => []
//            ],
            [
                'id' => '44694ace-12d7-42a6-8e4b-7dccf6dba434',
                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
                'name' => 'Aktionsregel: Wenn Nebenstatus zwischen "Zustand -1.000 bis -0.001" und "Zustand 1.000 bis 1.999"',
                'description' => '',
                'image' => 'flash_on',
                'visibility' => 1,
                'sort' => 0,
                'inputs' => [],
                'outputs' => [],
                'action_rules' => [
                    [
                        'id' => '9332bf4e-a176-400f-b8c7-d91fb570c5f2',
                        'action_type_id' => '44694ace-12d7-42a6-8e4b-7dccf6dba434',
                        'status_type_id' => '61fa7120-55cb-426a-a665-70ed42e31921',
                        'type' => ActionRule::TYPE_STATUS,
                        'operator' => ActionRule::OPERATOR_IN_BETWEEN,
                        'values' => [],
                        'state_ids' => ['c2d4e2c9-f942-4622-8a09-87d743c13582', '45b3d0d4-7af5-49ae-8103-8fa471f29d4f'],
                        'group' => 'default'
                    ],
                ],
                'status_rules' => [],
                'processors' => [],
                'components' => []
            ],
        ],
        'roles' => [
            [
                'id' => 'f33452ff-ad76-4eb7-b137-57cf4f16c6f9',
                'name' => 'Maintainer',
                'description' => 'Vollständigen Zugriff auf alle Daten, Aktionen, Status und Artefakte.',
                'active' => true,
                'permissions' => [
                    // Rechte bezüglich Prozess-Instanz.
                    [
                        'id' => 'a737ffd9-c550-4d48-aa6b-fc870c0a0508',
                        'name' => 'Prozess einsehen',
                        'description' => 'Einsehen der Prozess-Metadaten.',
                        'ident' => 'process_type.process.view',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'd8c0b54b-cf62-4281-b5d3-25d259f54169',
                        'name' => 'Alle Aktionen ausführen',
                        'description' => 'Ausführen aller Aktionen des Prozesses.',
                        'ident' => 'process_type.process.execute_actions',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'ae004262-4bbb-4e5c-b0eb-06644c33f186',
                        'name' => 'Situation einsehen',
                        'description' => 'Einsehen der aktuellen Prozess-Situation.',
                        'ident' => 'process_type.process.view_situation',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'e50ca135-8ba7-4493-bda5-fc6f21cdf2fb',
                        'name' => 'Zugriffe einsehen',
                        'description' => 'Einsehen der Benutzer und Gruppen die Zugriff auf den Prozess haben.',
                        'ident' => 'process_type.process.view_accesses',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'e924fc3e-5397-46ea-99ac-fc94615a376e',
                        'name' => 'Verlauf einsehen',
                        'description' => 'Aktionsverlauf und Situationsveränderungen einsehen.',
                        'ident' => 'process_type.process.view_history',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => '15df761a-b539-4d15-b584-1710e2ab3898',
                        'name' => 'Aktionen einsehen',
                        'description' => 'Alle Aktionen und Aktions-Komponenten einsehen.',
                        'ident' => 'process_type.process.view_actions',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'adaefdad-7244-474e-94b0-8557f08c291b',
                        'name' => 'Artefakte einsehen',
                        'description' => 'Einsehen der erzeugten Dokumente und E-Mails.',
                        'ident' => 'process_type.process.view_artifacts',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ]
                ]
            ]
        ],
        'list_configs' => [],
        'menu_items' => [],
        'templates' => [],
        'relation_types' => [],
    ],
    'autofahren' => [
        'name' => 'Autofahren',
        'namespace' => 'allisa',
        'identifier' => 'autofahren',
        'image' => 'directions_car',
        'version' => '1.0.0',
        'description' => '',
        'reference_pattern' => '',
        'action_type_mappings' => [],
        'default_role_id' => '156ede9a-2012-4311-a37f-8a7c5f064562',
        'published_at' => '2020-05-31 23:30:44',
        'categories' => [
            [
                'id' => 'f34b900d-c4b3-4726-a57a-14f2f35ac89f',
                'name' => 'Workflow',
                'description' => 'Prozess-Fortschritt bearbeiten.',
                'image' => 'settings',
                'color' => '#aad0ff'
            ]
        ],
        'outputs' => [],
        'status_types' => [
            [
                'id' => '07cc1dee-d886-4306-abfa-4e8092d69624',
                'reference' => '07cc1dee-d886-4306-abfa-4e8092d69624',
                'name' => 'Motor',
                'description' => 'Hauptzustand des Motors.',
                'namespace' => 'allisa',
                'identifier' => 'simple',
                'options' => [],
                'image' => 'settings',
                'sort' => 0,
                'size' => '6x1',
                'default' => '0.000',
                'states' => [
                    [
                        'id' => '5c4fd52d-6d8f-4066-b019-084e27eacb8b',
                        'status_type_id' => '07cc1dee-d886-4306-abfa-4e8092d69624',
                        'description' => 'Motor ist ausgeschaltet',
                        'image' => 'vpn_key',
                        'min' => '0.000',
                        'max' => '0.000',
                        'visible' => true,
                        'color' => '#72c6ff'
                    ],
                    [
                        'id' => '2c4a606d-784d-427b-9ff5-d094b68bb49a',
                        'status_type_id' => '07cc1dee-d886-4306-abfa-4e8092d69624',
                        'description' => 'Motor ist eingeschaltet',
                        'image' => 'vpn_key',
                        'min' => '1.000',
                        'max' => '1.000',
                        'visible' => true,
                        'color' => '#bae89e'
                    ],
                ]
            ],
            [
                'id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                'reference' => 'd6a75422-a67d-4108-a009-6a735c641831',
                'name' => 'Geschwindigkeit',
                'description' => 'Fahrtgeschwindigkeit des Autos.',
                'namespace' => 'allisa',
                'identifier' => 'simple',
                'options' => [],
                'image' => 'speed',
                'sort' => 0,
                'size' => '6x1',
                'default' => '0.000',
                'states' => [
                    [
                        'id' => '11a4c2d7-9f62-40b5-b5f2-efa0ac204f6f',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'description' => 'Auto steht',
                        'image' => 'speed',
                        'min' => '0.000',
                        'max' => '0.000',
                        'visible' => true,
                        'color' => '#bae89e'
                    ],
                    [
                        'id' => '99af34f0-6656-46df-bcca-ee64f90204e3',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'description' => 'Auto fährt mit geringer Geschwindigkeit',
                        'image' => 'speed',
                        'min' => '1.000',
                        'max' => '30.000',
                        'visible' => true,
                        'color' => '#7ac4b8'
                    ],
                    [
                        'id' => '7520912a-a7d0-4544-82ba-763e8c5bbd46',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'description' => 'Auto fährt mit mittlerer Geschwindigkeit',
                        'image' => 'speed',
                        'min' => '31.000',
                        'max' => '70.000',
                        'visible' => true,
                        'color' => '#c289ce'
                    ],
                    [
                        'id' => '2901d600-e2a9-460b-b37e-e7a838a820ad',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'description' => 'Auto fährt mit hoher Geschwindigkeit',
                        'image' => 'speed',
                        'min' => '71.000',
                        'max' => '110.000',
                        'visible' => true,
                        'color' => '#efa8a8'
                    ]
                ]
            ],
            [
                'id' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                'reference' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                'name' => 'Tankfüllstand',
                'description' => 'Anzeige des Tankfüllstandes.',
                'namespace' => 'allisa',
                'identifier' => 'simple',
                'options' => [],
                'image' => 'local_gas_station',
                'sort' => 0,
                'size' => '6x1',
                'default' => '50.000',
                'states' => [
                    [
                        'id' => 'f71e64c7-a967-4c86-86a5-60ebfe5eeb65',
                        'status_type_id' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                        'description' => 'Leer',
                        'image' => 'battery_alert',
                        'min' => '0.000',
                        'max' => '0.000',
                        'visible' => true,
                        'color' => '#efa8a8'
                    ],
                    [
                        'id' => 'e40576e4-10bd-4305-9d35-2c6e33375b71',
                        'status_type_id' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                        'description' => 'Fast leer - noch 1x beschleunigen möglich',
                        'image' => 'battery_alert',
                        'min' => '1.000',
                        'max' => '19.000',
                        'visible' => true,
                        'color' => '#f2e3a5'
                    ],
                    [
                        'id' => '7c226a77-23b7-40a7-94ce-26b021a1d1fa',
                        'status_type_id' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                        'description' => 'Halb voll - noch 2-3x beschleunigen möglich',
                        'image' => 'battery_alert',
                        'min' => '20.000',
                        'max' => '39.000',
                        'visible' => true,
                        'color' => '#bae89e'
                    ],
                    [
                        'id' => '087d7769-7370-4581-8d9e-e6a77146ef50',
                        'status_type_id' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                        'description' => 'Fast voll  - noch 4x beschleunigen möglich',
                        'image' => 'battery_full',
                        'min' => '40.000',
                        'max' => '49.000',
                        'visible' => true,
                        'color' => '#bae89e'
                    ],
                    [
                        'id' => '917d577f-cae6-498d-abd9-a876a2cbd113',
                        'status_type_id' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                        'description' => 'Voll - noch 5x beschleunigen möglich',
                        'image' => 'battery_full',
                        'min' => '50.000',
                        'max' => '50.000',
                        'visible' => true,
                        'color' => '#bae89e'
                    ]
                ]
            ],
            [
                'id' => '681b27fb-24e4-46d8-b021-0bc52c66960c',
                'reference' => '681b27fb-24e4-46d8-b021-0bc52c66960c',
                'name' => 'Radio',
                'description' => 'Zustand des Autoradios.',
                'namespace' => 'allisa',
                'identifier' => 'simple',
                'options' => [],
                'image' => 'radio',
                'sort' => 0,
                'size' => '6x1',
                'default' => '0.000',
                'states' => [
                    [
                        'id' => 'a0ebf677-374d-475e-b6c7-d2980944244b',
                        'status_type_id' => '681b27fb-24e4-46d8-b021-0bc52c66960c',
                        'description' => 'Radio ist ausgeschaltet',
                        'image' => 'volume_off',
                        'min' => '0.000',
                        'max' => '0.000',
                        'visible' => true,
                        'color' => '#72c6ff'
                    ],
                    [
                        'id' => '1f60e980-4dea-4986-8356-17eaf495dbda',
                        'status_type_id' => '681b27fb-24e4-46d8-b021-0bc52c66960c',
                        'description' => 'Radio ist eingeschaltet - leise Lautstärke',
                        'image' => 'volume_down',
                        'min' => '1.000',
                        'max' => '1.000',
                        'visible' => true,
                        'color' => '#f2e3a5'
                    ],
                    [
                        'id' => '2b10aff5-ed73-41fe-a336-aac2773464de',
                        'status_type_id' => '681b27fb-24e4-46d8-b021-0bc52c66960c',
                        'description' => 'Radio ist eingeschaltet - mittlere Lautstärke',
                        'image' => 'volume_down',
                        'min' => '2.000',
                        'max' => '2.000',
                        'visible' => true,
                        'color' => '#bae89e'
                    ],
                    [
                        'id' => 'b7e3e82e-620d-4288-9be3-a0b7f817e544',
                        'status_type_id' => '681b27fb-24e4-46d8-b021-0bc52c66960c',
                        'description' => 'Radio ist eingeschaltet - laute Lautstärke',
                        'image' => 'volume_up',
                        'min' => '3.000',
                        'max' => '3.000',
                        'visible' => true,
                        'color' => '#bae89e'
                    ],
                ]
            ]
        ],
        'action_types' => [
            [
                'id' => 'ac2066a6-6473-40f0-a2d3-469aebaf7517',
                'category_id' => 'f34b900d-c4b3-4726-a57a-14f2f35ac89f',
                'name' => 'Motor starten',
                'description' => 'Schaltet den Motor ein.',
                'image' => 'flash_on',
                'visibility' => 1,
                'sort' => 0,
                'inputs' => [],
                'outputs' => [],
                'action_rules' => [
                    // Motor gleich ausgeschaltet
                    [
                        'id' => 'd1ccf171-9f50-4fc3-a00c-d67a9d9eb692',
                        'action_type_id' => 'ac2066a6-6473-40f0-a2d3-469aebaf7517',
                        'status_type_id' => '07cc1dee-d886-4306-abfa-4e8092d69624',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'IN_ARRAY',
                        'values' => ['0.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ],
                    // Tankfüllstand größer leer
                    [
                        'id' => 'cffc1949-c1c9-42cc-9db5-38f6bfe1c491',
                        'action_type_id' => 'ac2066a6-6473-40f0-a2d3-469aebaf7517',
                        'status_type_id' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'GREATER',
                        'values' => ['0.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ]
                ],
                'status_rules' => [
                    // Motor gleich eingeschaltet
                    [
                        'id' => '9e3a90eb-e5de-4037-bd8c-fa9272089d45',
                        'action_type_id' => 'ac2066a6-6473-40f0-a2d3-469aebaf7517',
                        'status_type_id' => '07cc1dee-d886-4306-abfa-4e8092d69624',
                        'operator' => 'SET',
                        'output' => null,
                        'values' => ['1.000']
                    ],
                ],
                'processors' => [],
                'components' => []
            ],
            [
                'id' => '86e87b47-d586-4821-b479-df22f605a31c',
                'category_id' => 'f34b900d-c4b3-4726-a57a-14f2f35ac89f',
                'name' => 'Motor stoppen',
                'description' => 'Schaltet den Motor aus.',
                'image' => 'flash_on',
                'visibility' => 1,
                'sort' => 0,
                'inputs' => [],
                'outputs' => [],
                'action_rules' => [
                    // Motor gleich eingeschaltet
                    [
                        'id' => 'ad27f379-9dde-40da-922f-dcbc7f890451',
                        'action_type_id' => '86e87b47-d586-4821-b479-df22f605a31c',
                        'status_type_id' => '07cc1dee-d886-4306-abfa-4e8092d69624',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'IN_ARRAY',
                        'values' => ['1.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ],
                    // Geschwindigkeit gleich 0
                    [
                        'id' => 'cffc1949-c1c9-42cc-9db5-38f6bfe1c491',
                        'action_type_id' => '86e87b47-d586-4821-b479-df22f605a31c',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'IN_ARRAY',
                        'values' => ['0.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ]
                ],
                'status_rules' => [
                    // Motor gleich ausgeschaltet
                    [
                        'id' => 'a0861575-782f-4cad-9461-b951c585afba',
                        'action_type_id' => '86e87b47-d586-4821-b479-df22f605a31c',
                        'status_type_id' => '07cc1dee-d886-4306-abfa-4e8092d69624',
                        'operator' => 'SET',
                        'output' => null,
                        'values' => ['0.000']
                    ],
                    // Radio gleich ausgeschaltet
                    [
                        'id' => '044be779-21f9-423c-b9fa-94966841e271',
                        'action_type_id' => '86e87b47-d586-4821-b479-df22f605a31c',
                        'status_type_id' => '681b27fb-24e4-46d8-b021-0bc52c66960c',
                        'operator' => 'SET',
                        'output' => null,
                        'values' => ['0.000']
                    ]
                ],
                'processors' => [],
                'components' => []
            ],
            [
                'id' => 'b0e4565e-ab34-4796-9746-c84a55add3b5',
                'category_id' => 'f34b900d-c4b3-4726-a57a-14f2f35ac89f',
                'name' => 'Beschleunigen',
                'description' => 'Erhöht die Geschwindigkeit des Autos.',
                'image' => 'flash_on',
                'visibility' => 1,
                'sort' => 0,
                'inputs' => [],
                'outputs' => [],
                'action_rules' => [
                    // Motor gleich eingeschaltet
                    [
                        'id' => 'ad27f379-9dde-40da-922f-dcbc7f890451',
                        'action_type_id' => 'b0e4565e-ab34-4796-9746-c84a55add3b5',
                        'status_type_id' => '07cc1dee-d886-4306-abfa-4e8092d69624',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'IN_ARRAY',
                        'values' => ['1.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ],
                    // Tankfüllstand größer als 0
                    [
                        'id' => 'cffc1949-c1c9-42cc-9db5-38f6bfe1c491',
                        'action_type_id' => 'b0e4565e-ab34-4796-9746-c84a55add3b5',
                        'status_type_id' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'GREATER',
                        'values' => ['0.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ],
                    // Geschwindigkeit kleiner als 110
                    [
                        'id' => '31c37863-3ec8-4d7f-b0ab-a6a807003b0c',
                        'action_type_id' => 'b0e4565e-ab34-4796-9746-c84a55add3b5',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'LOWER',
                        'values' => ['110.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ]
                ],
                'status_rules' => [
                    // Geschwindigkeit plus 20
                    [
                        'id' => 'a0861575-782f-4cad-9461-b951c585afba',
                        'action_type_id' => 'b0e4565e-ab34-4796-9746-c84a55add3b5',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'operator' => 'ADD',
                        'output' => null,
                        'values' => ['20.000']
                    ],
                    // Tankfüllstand minus 10
                    [
                        'id' => '044be779-21f9-423c-b9fa-94966841e271',
                        'action_type_id' => 'b0e4565e-ab34-4796-9746-c84a55add3b5',
                        'status_type_id' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                        'operator' => 'SUB',
                        'output' => null,
                        'values' => ['10.000']
                    ]
                ],
                'processors' => [],
                'components' => []
            ],
            [
                'id' => 'aa3dad52-6f93-4d83-a94b-795be7073553',
                'category_id' => 'f34b900d-c4b3-4726-a57a-14f2f35ac89f',
                'name' => 'Bremsen',
                'description' => 'Verringert die Geschwindigkeit des Autos.',
                'image' => 'flash_on',
                'visibility' => 1,
                'sort' => 0,
                'inputs' => [],
                'outputs' => [],
                'action_rules' => [
                    // Geschwindigkeit größer als 0
                    [
                        'id' => '8c06fad2-aebc-4e82-ac2b-1b0b5d00de2e',
                        'action_type_id' => 'aa3dad52-6f93-4d83-a94b-795be7073553',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'GREATER',
                        'values' => ['0.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ]
                ],
                'status_rules' => [
                    // Geschwindigkeit minus 20
                    [
                        'id' => 'ab503277-0484-4657-b5d1-28cb49d49c9b',
                        'action_type_id' => 'aa3dad52-6f93-4d83-a94b-795be7073553',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'operator' => 'SUB',
                        'output' => null,
                        'values' => ['20.000']
                    ],
                ],
                'processors' => [],
                'components' => []
            ],
            [
                'id' => '89543f5f-eed7-4fbb-b3d8-8c02c591a1cc',
                'category_id' => 'f34b900d-c4b3-4726-a57a-14f2f35ac89f',
                'name' => 'Anhalten',
                'description' => 'Stoppt das Auto indem die Geschwindigkeit auf 0 gesetzt wird.',
                'image' => 'flash_on',
                'visibility' => 1,
                'sort' => 0,
                'inputs' => [],
                'outputs' => [],
                'action_rules' => [
                    // Geschwindigkeit größer als 0
                    [
                        'id' => 'cd2f60a0-5523-4040-ad7c-db1b5555fb1c',
                        'action_type_id' => '89543f5f-eed7-4fbb-b3d8-8c02c591a1cc',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'GREATER',
                        'values' => ['0.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ]
                ],
                'status_rules' => [
                    // Geschwindigkeit auf 0
                    [
                        'id' => '8497835c-30a4-44af-8f31-da7bacce2ba3',
                        'action_type_id' => '89543f5f-eed7-4fbb-b3d8-8c02c591a1cc',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'operator' => 'SET',
                        'output' => null,
                        'values' => ['0.000']
                    ],
                ],
                'processors' => [],
                'components' => []
            ],
            [
                'id' => '2d163f43-76d6-44cc-935e-13fc30e8aaa9',
                'category_id' => 'f34b900d-c4b3-4726-a57a-14f2f35ac89f',
                'name' => 'Tanken',
                'description' => 'Betankt das Auto',
                'image' => 'flash_on',
                'visibility' => 1,
                'sort' => 0,
                'inputs' => [],
                'outputs' => [],
                'action_rules' => [
                    // Geschwindigkeit gleich 0
                    [
                        'id' => 'b2d5013b-1fad-4a3a-a98e-94bf9dd2d600',
                        'action_type_id' => '2d163f43-76d6-44cc-935e-13fc30e8aaa9',
                        'status_type_id' => 'd6a75422-a67d-4108-a009-6a735c641831',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'IN_ARRAY',
                        'values' => ['0.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ],
                    // Tankfüllstand kleiner als 50
                    [
                        'id' => 'ade9bca5-7235-4184-b834-496fba06aab4',
                        'action_type_id' => '2d163f43-76d6-44cc-935e-13fc30e8aaa9',
                        'status_type_id' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'LOWER',
                        'values' => ['50.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ],
                ],
                'status_rules' => [
                    // Tankfüllstand auf 50
                    [
                        'id' => '490066c7-0afa-486b-b4f1-9ac22e33d9a6',
                        'action_type_id' => '2d163f43-76d6-44cc-935e-13fc30e8aaa9',
                        'status_type_id' => '358d0c04-6c7c-45f1-a65a-9b5affa3fff0',
                        'operator' => 'SET',
                        'output' => null,
                        'values' => ['50.000']
                    ],
                ],
                'processors' => [],
                'components' => []
            ],
            [
                'id' => 'ae7d1755-45f6-4cdd-845d-50b055e42752',
                'category_id' => 'f34b900d-c4b3-4726-a57a-14f2f35ac89f',
                'name' => 'Radio einschalten',
                'description' => 'Schaltet das Radio auf eine bestimmte Lautstärke.',
                'image' => 'flash_on',
                'visibility' => 1,
                'sort' => 0,
                'inputs' => [],
                'outputs' => [
                    [
                        'name' => 'lautstaerke',
                        'description' => 'Radio-Lautstärke',
                        'default' => '',
                        'access' => 2,
                        'validation' => ['required', 'in:1,2,3']
                    ],
                ],
                'action_rules' => [
                    // Motor gleich eingeschaltet
                    [
                        'id' => 'fe9424f2-17d8-4304-be97-0918ec3ecf24',
                        'action_type_id' => 'ae7d1755-45f6-4cdd-845d-50b055e42752',
                        'status_type_id' => '07cc1dee-d886-4306-abfa-4e8092d69624',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'IN_ARRAY',
                        'values' => ['1.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ],
                    // Radio gleich ausgeschaltet
                    [
                        'id' => 'a0856d62-a5f5-4382-ab83-52c4ca0c9245',
                        'action_type_id' => 'ae7d1755-45f6-4cdd-845d-50b055e42752',
                        'status_type_id' => '681b27fb-24e4-46d8-b021-0bc52c66960c',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'IN_ARRAY',
                        'values' => ['0.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ],
                ],
                'status_rules' => [
                    // Radio auf Feldwert 'lautstaerke'
                    [
                        'id' => '85aae4a3-dd1a-4c85-9d78-9dc566308dab',
                        'action_type_id' => 'ae7d1755-45f6-4cdd-845d-50b055e42752',
                        'status_type_id' => '681b27fb-24e4-46d8-b021-0bc52c66960c',
                        'operator' => 'SET',
                        'output' => 'lautstaerke',
                        'values' => []
                    ],
                ],
                'processors' => [],
                'components' => [
                    [
                        'id' => '45db8662-26dc-4945-be70-74430207852a',
                        'label' => 'app.form',
                        'namespace' => 'allisa',
                        'action_type_id' => 'ae7d1755-45f6-4cdd-845d-50b055e42752',
                        'sort' => 0,
                        'identifier' => 'form',
                        'options' => [
                            'width' => 12,
                            'sets' => [
                                [
                                    'label' => 'Anforderung',
                                    'sort' => 0,
                                    'width' => '12',
                                    'fields' => [
                                        [
                                            'type' => 'select',
                                            'name' => 'lautstaerke',
                                            'label' => 'Lautstärke',
                                            'helper_text' => '',
                                            'default' => '',
                                            'width' => 12,
                                            'validation' => '',
                                            'items' => [
                                                [
                                                    'label' => 'Bitte wählen..',
                                                    'value' => '',
                                                ],
                                                [
                                                    'label' => 'Keine',
                                                    'value' => '1.000'
                                                ],
                                                [
                                                    'label' => 'Niedrig',
                                                    'value' => '2.000'
                                                ],
                                                [
                                                    'label' => 'Mittel',
                                                    'value' => '3.000'
                                                ],
                                                [
                                                    'label' => 'Hoch',
                                                    'value' => '4.000'
                                                ],
                                            ]
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => '933e6dd2-8a70-41b2-87dc-9c53685b37b6',
                'category_id' => 'f34b900d-c4b3-4726-a57a-14f2f35ac89f',
                'name' => 'Radio ausschalten',
                'description' => 'Schaltet das Radio aus.',
                'image' => 'flash_on',
                'visibility' => 1,
                'sort' => 0,
                'inputs' => [],
                'outputs' => [],
                'action_rules' => [
                    // Radio entweder 1,2,3
                    [
                        'id' => '6f51e54f-124c-4384-910a-b7adc5949e56',
                        'action_type_id' => '933e6dd2-8a70-41b2-87dc-9c53685b37b6',
                        'status_type_id' => '681b27fb-24e4-46d8-b021-0bc52c66960c',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'IN_ARRAY',
                        'values' => ['1.000', '2.000', '3.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ],
                ],
                'status_rules' => [
                    // Radio gleich 0
                    [
                        'id' => '66d9b44f-02aa-445c-9021-b0a309e1b036',
                        'action_type_id' => '933e6dd2-8a70-41b2-87dc-9c53685b37b6',
                        'status_type_id' => '681b27fb-24e4-46d8-b021-0bc52c66960c',
                        'operator' => 'SET',
                        'output' => null,
                        'values' => ['0.000']
                    ],
                ],
                'processors' => [],
                'components' => []
            ],

        ],
        'roles' => [
            [
                'id' => '156ede9a-2012-4311-a37f-8a7c5f064562',
                'name' => 'Maintainer',
                'description' => 'Vollständigen Zugriff auf alle Daten, Aktionen, Status und Artefakte.',
                'active' => true,
                'permissions' => [
                    // Rechte bezüglich Prozess-Instanz.
                    [
                        'id' => 'a737ffd9-c550-4d48-aa6b-fc870c0a0508',
                        'name' => 'Prozess einsehen',
                        'description' => 'Einsehen der Prozess-Metadaten.',
                        'ident' => 'process_type.process.view',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'd8c0b54b-cf62-4281-b5d3-25d259f54169',
                        'name' => 'Alle Aktionen ausführen',
                        'description' => 'Ausführen aller Aktionen des Prozesses.',
                        'ident' => 'process_type.process.execute_actions',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'ae004262-4bbb-4e5c-b0eb-06644c33f186',
                        'name' => 'Situation einsehen',
                        'description' => 'Einsehen der aktuellen Prozess-Situation.',
                        'ident' => 'process_type.process.view_situation',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'e50ca135-8ba7-4493-bda5-fc6f21cdf2fb',
                        'name' => 'Zugriffe einsehen',
                        'description' => 'Einsehen der Benutzer und Gruppen die Zugriff auf den Prozess haben.',
                        'ident' => 'process_type.process.view_accesses',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => '9278d539-e62a-4011-a24b-8ea4342c8c8b',
                        'name' => 'Verlauf einsehen',
                        'description' => 'Aktionsverlauf und Situationsveränderungen einsehen.',
                        'ident' => 'process_type.process.view_history',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => '76446a71-26d6-4814-869a-deb54468bcec',
                        'name' => 'Aktionen einsehen',
                        'description' => 'Alle Aktionen und Aktions-Komponenten einsehen.',
                        'ident' => 'process_type.process.view_actions',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => '92442855-3393-428b-bc0d-982f2b519fea',
                        'name' => 'Artefakte einsehen',
                        'description' => 'Einsehen der erzeugten Dokumente und E-Mails.',
                        'ident' => 'process_type.process.view_artifacts',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ]
                ]
            ]
        ],
        'list_configs' => [],
        'menu_items' => [],
        'templates' => [],
        'relation_types' => [],
    ],
    'initial' => [
        'name' => 'Initial',
        'namespace' => 'allisa',
        'identifier' => 'initial',
        'image' => 'star',
        'version' => '1.0.0',
        'description' => '',
        'action_type_mappings' => [],
        'reference_pattern' => '',
        'default_role_id' => 'd2448425-a44d-4482-833b-bb6a96f0b243',
        'published_at' => '2020-05-31 23:30:44',
        'categories' => [
            [
                'id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
                'name' => 'Workflow',
                'description' => 'Prozess-Fortschritt bearbeiten.',
                'image' => 'settings',
                'color' => '#aad0ff'
            ]
        ],
        'outputs' => [],
        'status_types' => [
            [
                'id' => 'c1430309-91b3-46f5-9935-d3bb30b5952b',
                'reference' => 'c1430309-91b3-46f5-9935-d3bb30b5952b',
                'name' => 'Hauptstatus',
                'description' => 'Die Beschreibung des Hauptstatus.',
                'namespace' => 'allisa',
                'identifier' => 'simple',
                'options' => [],
                'process_type_id' => 'd9f78520-3c9e-4c3f-8220-a98bc7429069',
                'image' => 'grain',
                'sort' => 0,
                'size' => '2x1',
                'default' => '0.000',
                'states' => [
                    [
                        'id' => '11bcfb6a-d6f4-402b-a2d8-64fbbcca0f7b',
                        'status_type_id' => 'c1430309-91b3-46f5-9935-d3bb30b5952b',
                        'description' => 'Startzustand',
                        'image' => 'play_arrow',
                        'min' => '0.000',
                        'max' => '0.000',
                        'visible' => true,
                        'color' => '#FF4B44'
                    ],
                    [
                        'id' => '913c4e04-7a37-4379-89c0-658276114bcd',
                        'status_type_id' => 'c1430309-91b3-46f5-9935-d3bb30b5952b',
                        'description' => 'Zustand mit Wert 1',
                        'image' => 'star',
                        'min' => '1.000',
                        'max' => '1.000',
                        'visible' => true,
                        'color' => '#6583FF'
                    ],
                    [
                        'id' => '75d8466f-7c0e-425c-b6d0-01579067314b',
                        'status_type_id' => 'c1430309-91b3-46f5-9935-d3bb30b5952b',
                        'description' => 'Zustand mit Wert 2',
                        'image' => 'star',
                        'min' => '2.000',
                        'max' => '2.000',
                        'visible' => true,
                        'color' => '#6583FF'
                    ]
                ]
            ],
            [
                'id' => 'e24f002e-25d2-4388-a180-a9d83975a3a8',
                'reference' => 'e24f002e-25d2-4388-a180-a9d83975a3a8',
                'name' => 'Nebenstatus',
                'description' => 'Die Beschreibung des Nebenstatus.',
                'namespace' => 'allisa',
                'identifier' => 'simple',
                'options' => [],
                'process_type_id' => 'd9f78520-3c9e-4c3f-8220-a98bc7429069',
                'image' => 'grain',
                'sort' => 0,
                'size' => '2x1',
                'default' => '0.000',
                'states' => [
                    [
                        'id' => '11bcfb6a-d6f4-402b-a2d8-64fbbcca0f7b',
                        'status_type_id' => 'e24f002e-25d2-4388-a180-a9d83975a3a8',
                        'description' => 'Nebenstatus Zustand 0',
                        'image' => 'play_arrow',
                        'min' => '0.000',
                        'max' => '0.000',
                        'visible' => true,
                        'color' => '#FF4B44'
                    ],
                    [
                        'id' => '913c4e04-7a37-4379-89c0-658276114bcd',
                        'status_type_id' => 'e24f002e-25d2-4388-a180-a9d83975a3a8',
                        'description' => 'Nebenstatus Zustand mit Wert 1',
                        'image' => 'star',
                        'min' => '1.000',
                        'max' => '1.000',
                        'visible' => true,
                        'color' => '#6583FF'
                    ],
                    [
                        'id' => '2fa463d1-50bc-40e0-a04d-65ad0ddefe4f',
                        'status_type_id' => 'e24f002e-25d2-4388-a180-a9d83975a3a8',
                        'description' => 'Nebenstatus Zustand mit Wert 2',
                        'image' => 'star',
                        'min' => '2.000',
                        'max' => '2.000',
                        'visible' => true,
                        'color' => '#35495e'
                    ]
                ]
            ]
        ],
        'action_types' => [
            [
                'id' => '84714030-4a18-468f-a83d-7de1bde5cd1e',
                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
                'name' => 'Startaktion',
                'description' => '',
                'image' => 'flash_on',
                'visibility' => 1,
                'sort' => 0,
                'inputs' => [],
                'outputs' => [],
                'action_rules' => [
                    [
                        'id' => 'bcbb9b32-8a44-4055-a981-82d6920324b3',
                        'action_type_id' => '84714030-4a18-468f-a83d-7de1bde5cd1e',
                        'status_type_id' => 'c1430309-91b3-46f5-9935-d3bb30b5952b',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'IN_ARRAY',
                        'values' => ['0.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ],
                    [
                        'id' => 'cffc1949-c1c9-42cc-9db5-38f6bfe1c491',
                        'action_type_id' => '84714030-4a18-468f-a83d-7de1bde5cd1e',
                        'status_type_id' => 'e24f002e-25d2-4388-a180-a9d83975a3a8',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'IN_ARRAY',
                        'values' => ['0.000', '1.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ]
                ],
                'status_rules' => [
                    [
                        'id' => '79c56f48-af33-4127-b532-f9d3158867fd',
                        'action_type_id' => '84714030-4a18-468f-a83d-7de1bde5cd1e',
                        'status_type_id' => 'c1430309-91b3-46f5-9935-d3bb30b5952b',
                        'operator' => 'SET',
                        'output' => null,
                        'values' => ['1.000']
                    ],
                    [
                        'id' => '12b8fcde-7418-47d6-a311-f629dca68877',
                        'action_type_id' => '84714030-4a18-468f-a83d-7de1bde5cd1e',
                        'status_type_id' => 'e24f002e-25d2-4388-a180-a9d83975a3a8',
                        'operator' => 'SET',
                        'output' => null,
                        'values' => ['1.000']
                    ]
                ],
                'processors' => [],
                'components' => []
            ],
            [
                'id' => '4d7116d4-eeaf-4464-81b6-035589d45c8b',
                'category_id' => 'a72233a8-17d2-4b45-aa2a-c0fed4ab92b5',
                'name' => 'Aktion 1',
                'description' => 'Setzt den Hauptstatus auf 2.',
                'image' => 'flash_on',
                'visibility' => 1,
                'sort' => 0,
                'inputs' => [],
                'outputs' => [],
                'action_rules' => [
                    [
                        'id' => '9c9dc5f6-79b2-493f-b72c-4e7c8fea6b8d',
                        'action_type_id' => '4d7116d4-eeaf-4464-81b6-035589d45c8b',
                        'status_type_id' => 'c1430309-91b3-46f5-9935-d3bb30b5952b',
                        'type' => 'TYPE_STATUS',
                        'operator' => 'IN_ARRAY',
                        'values' => ['1.000'],
                        'state_ids' => [],
                        'group' => 'default'
                    ]
                ],
                'status_rules' => [
                    [
                        'id' => '6839ba42-6771-4c3f-b4d4-38b5919edd6f',
                        'action_type_id' => '4d7116d4-eeaf-4464-81b6-035589d45c8b',
                        'status_type_id' => 'c1430309-91b3-46f5-9935-d3bb30b5952b',
                        'operator' => 'SET',
                        'output' => null,
                        'values' => ['2.000']
                    ],
                    [
                        'id' => '38482969-5e8e-45c9-9f8c-294232f3a18d',
                        'action_type_id' => '4d7116d4-eeaf-4464-81b6-035589d45c8b',
                        'status_type_id' => 'e24f002e-25d2-4388-a180-a9d83975a3a8',
                        'operator' => 'ADD',
                        'output' => null,
                        'values' => ['2.500']
                    ]
                ],
                'processors' => [],
                'components' => []
            ],
        ],
        'roles' => [
            [
                'id' => '156ede9a-2012-4311-a37f-8a7c5f064562',
                'name' => 'Maintainer',
                'description' => 'Vollständigen Zugriff auf alle Daten, Aktionen, Status und Artefakte.',
                'active' => true,
                'permissions' => [
                    // Rechte bezüglich Prozess-Instanz.
                    [
                        'id' => 'a737ffd9-c550-4d48-aa6b-fc870c0a0508',
                        'name' => 'Prozess einsehen',
                        'description' => 'Einsehen der Prozess-Metadaten.',
                        'ident' => 'process_type.process.view',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'd8c0b54b-cf62-4281-b5d3-25d259f54169',
                        'name' => 'Alle Aktionen ausführen',
                        'description' => 'Ausführen aller Aktionen des Prozesses.',
                        'ident' => 'process_type.process.execute_actions',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'ae004262-4bbb-4e5c-b0eb-06644c33f186',
                        'name' => 'Situation einsehen',
                        'description' => 'Einsehen der aktuellen Prozess-Situation.',
                        'ident' => 'process_type.process.view_situation',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'e50ca135-8ba7-4493-bda5-fc6f21cdf2fb',
                        'name' => 'Zugriffe einsehen',
                        'description' => 'Einsehen der Benutzer und Gruppen die Zugriff auf den Prozess haben.',
                        'ident' => 'process_type.process.view_accesses',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'e924fc3e-5397-46ea-99ac-fc94615a376e',
                        'name' => 'Verlauf einsehen',
                        'description' => 'Aktionsverlauf und Situationsveränderungen einsehen.',
                        'ident' => 'process_type.process.view_history',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => '15df761a-b539-4d15-b584-1710e2ab3898',
                        'name' => 'Aktionen einsehen',
                        'description' => 'Alle Aktionen und Aktions-Komponenten einsehen.',
                        'ident' => 'process_type.process.view_actions',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ],
                    [
                        'id' => 'adaefdad-7244-474e-94b0-8557f08c291b',
                        'name' => 'Artefakte einsehen',
                        'description' => 'Einsehen der erzeugten Dokumente und E-Mails.',
                        'ident' => 'process_type.process.view_artifacts',
                        'scope' => 'App\ProcessType',
                        'conditions' => [],
                    ]
                ]
            ]
        ],
        'list_configs' => [],
        'menu_items' => [],
        'templates' => [],
        'relation_types' => [],
        'events' => [],
        'listeners' => [],
        'javascript' => [],
        'dependencies' => [
            'process_types' => [],
            'action_type_plugins' => [],
            'status_type_plugins' => []
        ],
        'unique' => []
    ],
    'initial_calculated' => [
        'nodes' => [
            [
                'data' => [
                    'id' => '6ae4b203-fce7-403d-84a9-bafa34f998b0',
                    'name' => 'Hauptstatus',
                    'type' => 'status',
                    'model_id' => 'c1430309-91b3-46f5-9935-d3bb30b5952b'
                ],
                'position' => [
                    'x' => 35.767465574486,
                    'y' => 62.193024308718
                ],
                'group' => 'nodes',
                'removed' => false,
                'selected' => false,
                'selectable' => true,
                'locked' => false,
                'grabbable' => true,
                'pannable' => false,
                'classes' => 'status'
            ],
            [
                'data' => [
                    'id' => 'f817fac4-05f6-4f58-8fef-1698bbcd1f8c',
                    'parent' => '6ae4b203-fce7-403d-84a9-bafa34f998b0',
                    'name' => 'Startzustand',
                    'type' => 'state',
                    'model_id' => '11bcfb6a-d6f4-402b-a2d8-64fbbcca0f7b'
                ],
                'position' => [
                    'x' => -46.690081033409,
                    'y' => -49.35808566389
                ],
                'group' => 'nodes',
                'removed' => false,
                'selected' => false,
                'selectable' => true,
                'locked' => false,
                'grabbable' => true,
                'pannable' => false,
                'classes' => 'state'
            ],
            [
                'data' => [
                    'id' => 'a1e85539-3b3b-4f8d-bc0b-8092e8c4c49a',
                    'parent' => '6ae4b203-fce7-403d-84a9-bafa34f998b0',
                    'name' => 'Zustand mit Wert 1',
                    'type' => 'state',
                    'model_id' => '913c4e04-7a37-4379-89c0-658276114bcd'
                ],
                'position' => [
                    'x' => -42.688074087688,
                    'y' => 124.37282042156
                ],
                'group' => 'nodes',
                'removed' => false,
                'selected' => false,
                'selectable' => true,
                'locked' => false,
                'grabbable' => true,
                'pannable' => false,
                'classes' => 'state'
            ],
            [
                'data' => [
                    'id' => '3a1565d6-7c60-4ddb-a353-9de419fc8e72',
                    'name' => 'Startaktion',
                    'type' => 'action',
                    'model_id' => '84714030-4a18-468f-a83d-7de1bde5cd1e',
                    'parent' => '6ae4b203-fce7-403d-84a9-bafa34f998b0'
                ],
                'position' => [
                    'x' => 139.72300523666,
                    'y' => 19.741035887529
                ],
                'group' => 'nodes',
                'removed' => false,
                'selected' => false,
                'selectable' => true,
                'locked' => false,
                'grabbable' => true,
                'pannable' => false,
                'classes' => 'action'
            ],
            [
                'data' => [
                    'id' => 'ae40745c-76d2-4ef3-ad7d-34d10f2d7e46',
                    'name' => 'Aktion 1',
                    'type' => 'action',
                    'model_id' => '4d7116d4-eeaf-4464-81b6-035589d45c8b',
                    'parent' => '6ae4b203-fce7-403d-84a9-bafa34f998b0'
                ],
                'position' => [
                    'x' => 136.44530472916,
                    'y' => 194.24413428133
                ],
                'group' => 'nodes',
                'removed' => false,
                'selected' => false,
                'selectable' => true,
                'locked' => false,
                'grabbable' => true,
                'pannable' => false,
                'classes' => 'action'
            ]
        ],
        'edges' => [
            [
                'data' => [
                    'id' => 'f817fac4-05f6-4f58-8fef-1698bbcd1f8c 3a1565d6-7c60-4ddb-a353-9de419fc8e72',
                    'source' => 'f817fac4-05f6-4f58-8fef-1698bbcd1f8c',
                    'target' => '3a1565d6-7c60-4ddb-a353-9de419fc8e72'
                ],
                'position' => [
                    'x' => 0,
                    'y' => 0
                ],
                'group' => 'edges',
                'removed' => false,
                'selected' => false,
                'selectable' => true,
                'locked' => false,
                'grabbable' => true,
                'pannable' => true,
                'classes' => 'edge'
            ],
            [
                'data' => [
                    'id' => 'a1e85539-3b3b-4f8d-bc0b-8092e8c4c49a ae40745c-76d2-4ef3-ad7d-34d10f2d7e46',
                    'source' => 'a1e85539-3b3b-4f8d-bc0b-8092e8c4c49a',
                    'target' => 'ae40745c-76d2-4ef3-ad7d-34d10f2d7e46'
                ],
                'position' => [
                    'x' => 0,
                    'y' => 0
                ],
                'group' => 'edges',
                'removed' => false,
                'selected' => false,
                'selectable' => true,
                'locked' => false,
                'grabbable' => true,
                'pannable' => true,
                'classes' => 'edge'
            ],
            [
                'data' => [
                    'id' => '3a1565d6-7c60-4ddb-a353-9de419fc8e72 a1e85539-3b3b-4f8d-bc0b-8092e8c4c49a',
                    'source' => '3a1565d6-7c60-4ddb-a353-9de419fc8e72',
                    'target' => 'a1e85539-3b3b-4f8d-bc0b-8092e8c4c49a'
                ],
                'position' => [
                    'x' => 0,
                    'y' => 0
                ],
                'group' => 'edges',
                'removed' => false,
                'selected' => false,
                'selectable' => true,
                'locked' => false,
                'grabbable' => true,
                'pannable' => true,
                'classes' => 'edge'
            ]
        ]
    ],
    'autofahren_calculated' => [
        [
            "data" => [
                "id" => "dcbe5e7c-5bf4-4794-ac0f-5cf4dff42d45",
                "name" => "Motor",
                "type" => "status",
                "model_id" => "07cc1dee-d886-4306-abfa-4e8092d69624"
            ],
            "position" => [
                "x" => 1515.9628400962,
                "y" => 644.53362740654
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "status"
        ],
        [
            "data" => [
                "id" => "081df5b2-e09e-435b-bc1a-67585a45aa6b",
                "name" => "Motor starten",
                "type" => "action",
                "model_id" => "ac2066a6-6473-40f0-a2d3-469aebaf7517",
                "parent" => "dcbe5e7c-5bf4-4794-ac0f-5cf4dff42d45",
                "status_type_id" => "07cc1dee-d886-4306-abfa-4e8092d69624"
            ],
            "position" => [
                "x" => 1456.3619031594,
                "y" => 555.08259393847
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "b96ad115-3798-4ea7-ac64-d7d91a3360c2",
                "name" => "Motor stoppen",
                "type" => "action",
                "model_id" => "86e87b47-d586-4821-b479-df22f605a31c",
                "parent" => "dcbe5e7c-5bf4-4794-ac0f-5cf4dff42d45",
                "status_type_id" => "07cc1dee-d886-4306-abfa-4e8092d69624"
            ],
            "position" => [
                "x" => 1455.4578226654,
                "y" => 754.4846608746
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "851191e3-d4a6-4051-bf98-64902ee37bf4",
                "name" => "Beschleunigen",
                "type" => "action",
                "model_id" => "b0e4565e-ab34-4796-9746-c84a55add3b5",
                "parent" => "dcbe5e7c-5bf4-4794-ac0f-5cf4dff42d45",
                "status_type_id" => "07cc1dee-d886-4306-abfa-4e8092d69624"
            ],
            "position" => [
                "x" => 1674.9716644194,
                "y" => 712.94124061135
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "b2cd9892-baab-45da-972d-4540046fedf4",
                "name" => "Radio einschalten",
                "type" => "action",
                "model_id" => "ae7d1755-45f6-4cdd-845d-50b055e42752",
                "parent" => "dcbe5e7c-5bf4-4794-ac0f-5cf4dff42d45",
                "status_type_id" => "07cc1dee-d886-4306-abfa-4e8092d69624"
            ],
            "position" => [
                "x" => 1665.1220433735,
                "y" => 571.07708202236
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "973af91e-5ad0-441a-bfb0-3d89f8b6eb0c",
                "parent" => "dcbe5e7c-5bf4-4794-ac0f-5cf4dff42d45",
                "name" => "Motor ist ausgeschaltet",
                "model_id" => "5c4fd52d-6d8f-4066-b019-084e27eacb8b",
                "status_type_id" => "07cc1dee-d886-4306-abfa-4e8092d69624",
                "type" => "state"
            ],
            "position" => [
                "x" => 1356.3036368188,
                "y" => 656.33618239756
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "95e9fa68-2a9b-4f24-88ab-b1b4bc6215ce",
                "parent" => "dcbe5e7c-5bf4-4794-ac0f-5cf4dff42d45",
                "name" => "Motor ist eingeschaltet",
                "model_id" => "2c4a606d-784d-427b-9ff5-d094b68bb49a",
                "status_type_id" => "07cc1dee-d886-4306-abfa-4e8092d69624",
                "type" => "state"
            ],
            "position" => [
                "x" => 1555.7062573211,
                "y" => 661.75781830324
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "29ddecb7-4df8-44c7-b7b7-4f365df8f091",
                "name" => "Geschwindigkeit",
                "type" => "status",
                "model_id" => "d6a75422-a67d-4108-a009-6a735c641831"
            ],
            "position" => [
                "x" => 797.03525651919,
                "y" => 1194.9959302652
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "status"
        ],
        [
            "data" => [
                "id" => "84746af3-f2ae-44a7-a1af-f49579a08fdd",
                "name" => "Motor stoppen",
                "type" => "action",
                "model_id" => "86e87b47-d586-4821-b479-df22f605a31c",
                "parent" => "29ddecb7-4df8-44c7-b7b7-4f365df8f091",
                "status_type_id" => "d6a75422-a67d-4108-a009-6a735c641831"
            ],
            "position" => [
                "x" => 961.2095693261,
                "y" => 1034.2025333628
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "1f1b3b5c-52a2-480f-86ca-7267c1575e77",
                "name" => "Beschleunigen",
                "type" => "action",
                "model_id" => "b0e4565e-ab34-4796-9746-c84a55add3b5",
                "parent" => "29ddecb7-4df8-44c7-b7b7-4f365df8f091",
                "status_type_id" => "d6a75422-a67d-4108-a009-6a735c641831"
            ],
            "position" => [
                "x" => 780.3029140125,
                "y" => 1115.3211941526
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "9d15f1a2-af6d-4140-9653-d381dc45bfdb",
                "name" => "Bremsen",
                "type" => "action",
                "model_id" => "aa3dad52-6f93-4d83-a94b-795be7073553",
                "parent" => "29ddecb7-4df8-44c7-b7b7-4f365df8f091",
                "status_type_id" => "d6a75422-a67d-4108-a009-6a735c641831"
            ],
            "position" => [
                "x" => 694.56433756863,
                "y" => 1192.0217741881
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "74fa007e-337d-4c91-9a97-c5a6b81de365",
                "name" => "Anhalten",
                "type" => "action",
                "model_id" => "89543f5f-eed7-4fbb-b3d8-8c02c591a1cc",
                "parent" => "29ddecb7-4df8-44c7-b7b7-4f365df8f091",
                "status_type_id" => "d6a75422-a67d-4108-a009-6a735c641831"
            ],
            "position" => [
                "x" => 921.34242041485,
                "y" => 1115.4697642308
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "d19f3a80-bd1b-480d-9bfa-7d316664e0a1",
                "name" => "Tanken",
                "type" => "action",
                "model_id" => "2d163f43-76d6-44cc-935e-13fc30e8aaa9",
                "parent" => "29ddecb7-4df8-44c7-b7b7-4f365df8f091",
                "status_type_id" => "d6a75422-a67d-4108-a009-6a735c641831"
            ],
            "position" => [
                "x" => 751.96847954231,
                "y" => 1016.7585835573
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "c423ff8a-0b77-42af-b71d-3ae26573bee9",
                "parent" => "29ddecb7-4df8-44c7-b7b7-4f365df8f091",
                "name" => "Auto steht",
                "model_id" => "11a4c2d7-9f62-40b5-b5f2-efa0ac204f6f",
                "status_type_id" => "d6a75422-a67d-4108-a009-6a735c641831",
                "type" => "state"
            ],
            "position" => [
                "x" => 848.63740439804,
                "y" => 1004.1722006267
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "6df1d35a-e52c-4341-a00c-501451d7145f",
                "parent" => "29ddecb7-4df8-44c7-b7b7-4f365df8f091",
                "name" => "+20.000",
                "model_id" => "a0861575-782f-4cad-9461-b951c585afba",
                "status_type_id" => "d6a75422-a67d-4108-a009-6a735c641831",
                "action_type_id" => "b0e4565e-ab34-4796-9746-c84a55add3b5",
                "type" => "statusrule-node"
            ],
            "position" => [
                "x" => 610.46701537553,
                "y" => 1009.9965469883
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "statusrule-node"
        ],
        [
            "data" => [
                "id" => "677b4b1d-1e80-4154-a529-530962d01570",
                "parent" => "29ddecb7-4df8-44c7-b7b7-4f365df8f091",
                "name" => "-20.000",
                "model_id" => "ab503277-0484-4657-b5d1-28cb49d49c9b",
                "status_type_id" => "d6a75422-a67d-4108-a009-6a735c641831",
                "action_type_id" => "aa3dad52-6f93-4d83-a94b-795be7073553",
                "type" => "statusrule-node"
            ],
            "position" => [
                "x" => 610,
                "y" => 1141.8427802674
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "statusrule-node"
        ],
        [
            "data" => [
                "id" => "e7cc89a9-ae78-465f-b619-e40d7482204a",
                "parent" => "29ddecb7-4df8-44c7-b7b7-4f365df8f091",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 797.91054410399,
                "y" => 1338.4276738085
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-one"
        ],
        [
            "data" => [
                "id" => "732179bd-7315-45a2-962c-de321b9355dc",
                "parent" => "e7cc89a9-ae78-465f-b619-e40d7482204a",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 854.1046145093,
                "y" => 1338.4276738085
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-two"
        ],
        [
            "data" => [
                "id" => "b017e672-dd71-4a45-b32b-ab1dd6023bb9",
                "parent" => "732179bd-7315-45a2-962c-de321b9355dc",
                "name" => "Auto fährt mit geringer Geschwindigkeit",
                "model_id" => "99af34f0-6656-46df-bcca-ee64f90204e3",
                "status_type_id" => "d6a75422-a67d-4108-a009-6a735c641831",
                "type" => "state"
            ],
            "position" => [
                "x" => 913.60349766284,
                "y" => 1363.3196599037
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-two"
        ],
        [
            "data" => [
                "id" => "a7ae5a90-277d-4112-b728-709015d35d94",
                "parent" => "732179bd-7315-45a2-962c-de321b9355dc",
                "name" => "Auto fährt mit mittlerer Geschwindigkeit",
                "model_id" => "7520912a-a7d0-4544-82ba-763e8c5bbd46",
                "status_type_id" => "d6a75422-a67d-4108-a009-6a735c641831",
                "type" => "state"
            ],
            "position" => [
                "x" => 794.60573135575,
                "y" => 1363.0356877133
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-two"
        ],
        [
            "data" => [
                "id" => "c6031994-f454-4897-9b24-9b309444b385",
                "parent" => "e7cc89a9-ae78-465f-b619-e40d7482204a",
                "name" => "Auto fährt mit hoher Geschwindigkeit",
                "model_id" => "2901d600-e2a9-460b-b37e-e7a838a820ad",
                "status_type_id" => "d6a75422-a67d-4108-a009-6a735c641831",
                "type" => "state"
            ],
            "position" => [
                "x" => 660.71759054515,
                "y" => 1365.3488007589
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "fb283ae9-6190-4bd6-90fa-14ad0ecd1bbc",
                "name" => "Tankfüllstand",
                "type" => "status",
                "model_id" => "358d0c04-6c7c-45f1-a65a-9b5affa3fff0"
            ],
            "position" => [
                "x" => 1470.3303943506,
                "y" => 1143.5125590325
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "status"
        ],
        [
            "data" => [
                "id" => "488e1201-3917-489b-ae96-fbf6a209fdb7",
                "name" => "Motor starten",
                "type" => "action",
                "model_id" => "ac2066a6-6473-40f0-a2d3-469aebaf7517",
                "parent" => "fb283ae9-6190-4bd6-90fa-14ad0ecd1bbc",
                "status_type_id" => "358d0c04-6c7c-45f1-a65a-9b5affa3fff0"
            ],
            "position" => [
                "x" => 1692.7871038905,
                "y" => 1169.5460617312
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "5860993b-f7a0-4f43-8c8b-ba31b0fbd9b7",
                "name" => "Beschleunigen",
                "type" => "action",
                "model_id" => "b0e4565e-ab34-4796-9746-c84a55add3b5",
                "parent" => "fb283ae9-6190-4bd6-90fa-14ad0ecd1bbc",
                "status_type_id" => "358d0c04-6c7c-45f1-a65a-9b5affa3fff0"
            ],
            "position" => [
                "x" => 1411.089731439,
                "y" => 1333.5520365797
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "00dc4ced-7381-4e94-b3c2-d9c67c556419",
                "name" => "Tanken",
                "type" => "action",
                "model_id" => "2d163f43-76d6-44cc-935e-13fc30e8aaa9",
                "parent" => "fb283ae9-6190-4bd6-90fa-14ad0ecd1bbc",
                "status_type_id" => "358d0c04-6c7c-45f1-a65a-9b5affa3fff0"
            ],
            "position" => [
                "x" => 1688.0395595329,
                "y" => 1055.3848278309
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "75bade5d-66d7-4dff-8070-da46c567e439",
                "parent" => "fb283ae9-6190-4bd6-90fa-14ad0ecd1bbc",
                "name" => "Leer",
                "model_id" => "f71e64c7-a967-4c86-86a5-60ebfe5eeb65",
                "status_type_id" => "358d0c04-6c7c-45f1-a65a-9b5affa3fff0",
                "type" => "state"
            ],
            "position" => [
                "x" => 1689.6642438728,
                "y" => 957.75901834916
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "aaba84a1-6b44-456b-a6ec-fd2363e9d05a",
                "parent" => "fb283ae9-6190-4bd6-90fa-14ad0ecd1bbc",
                "name" => "-10.000",
                "model_id" => "044be779-21f9-423c-b9fa-94966841e271",
                "status_type_id" => "358d0c04-6c7c-45f1-a65a-9b5affa3fff0",
                "action_type_id" => "b0e4565e-ab34-4796-9746-c84a55add3b5",
                "type" => "statusrule-node"
            ],
            "position" => [
                "x" => 1520.9084995577,
                "y" => 1349.7660997158
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "statusrule-node"
        ],
        [
            "data" => [
                "id" => "540a157f-e222-48f7-8bff-cadbcb97bba2",
                "parent" => "fb283ae9-6190-4bd6-90fa-14ad0ecd1bbc",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 1418.9509505442,
                "y" => 1089.6571471676
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-one"
        ],
        [
            "data" => [
                "id" => "677636ca-7fa3-4c9b-a0de-b636a64caa6d",
                "parent" => "540a157f-e222-48f7-8bff-cadbcb97bba2",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 1418.9509505442,
                "y" => 1158.5318804271
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-two"
        ],
        [
            "data" => [
                "id" => "78908c02-89a6-47c7-b9f5-49d7827221d4",
                "parent" => "677636ca-7fa3-4c9b-a0de-b636a64caa6d",
                "name" => "Fast leer - noch 1x beschleunigen möglich",
                "model_id" => "e40576e4-10bd-4305-9d35-2c6e33375b71",
                "status_type_id" => "358d0c04-6c7c-45f1-a65a-9b5affa3fff0",
                "type" => "state"
            ],
            "position" => [
                "x" => 1305.8736848107,
                "y" => 1185.4364118941
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-two"
        ],
        [
            "data" => [
                "id" => "78d641cd-4434-4393-9118-d276a80dd6a3",
                "parent" => "677636ca-7fa3-4c9b-a0de-b636a64caa6d",
                "name" => "Halb voll - noch 2-3x beschleunigen möglich",
                "model_id" => "7c226a77-23b7-40a7-94ce-26b021a1d1fa",
                "status_type_id" => "358d0c04-6c7c-45f1-a65a-9b5affa3fff0",
                "type" => "state"
            ],
            "position" => [
                "x" => 1419.5858892319,
                "y" => 1190.7818804271
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-two"
        ],
        [
            "data" => [
                "id" => "f6c05c00-28c3-4715-9521-7c5937a81075",
                "parent" => "677636ca-7fa3-4c9b-a0de-b636a64caa6d",
                "name" => "Fast voll  - noch 4x beschleunigen möglich",
                "model_id" => "087d7769-7370-4581-8d9e-e6a77146ef50",
                "status_type_id" => "358d0c04-6c7c-45f1-a65a-9b5affa3fff0",
                "type" => "state"
            ],
            "position" => [
                "x" => 1529.5282162777,
                "y" => 1186.7242074728
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-two"
        ],
        [
            "data" => [
                "id" => "faf49074-3211-49c8-b626-d96d72daf8e0",
                "parent" => "540a157f-e222-48f7-8bff-cadbcb97bba2",
                "name" => "Voll - noch 5x beschleunigen möglich",
                "model_id" => "917d577f-cae6-498d-abd9-a876a2cbd113",
                "status_type_id" => "358d0c04-6c7c-45f1-a65a-9b5affa3fff0",
                "type" => "state"
            ],
            "position" => [
                "x" => 1426.2950966804,
                "y" => 1016.5324139081
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "55afa3c0-d907-42bb-8ece-e489df25494f",
                "name" => "Radio",
                "type" => "status",
                "model_id" => "681b27fb-24e4-46d8-b021-0bc52c66960c"
            ],
            "position" => [
                "x" => 1018.6831806143,
                "y" => 435.86672422717
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "status"
        ],
        [
            "data" => [
                "id" => "253898e7-b6e9-4cd5-884e-32755baa6bac",
                "name" => "Motor stoppen",
                "type" => "action",
                "model_id" => "86e87b47-d586-4821-b479-df22f605a31c",
                "parent" => "55afa3c0-d907-42bb-8ece-e489df25494f",
                "status_type_id" => "681b27fb-24e4-46d8-b021-0bc52c66960c"
            ],
            "position" => [
                "x" => 1136.9998201922,
                "y" => 626.23130179216
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "14d1d2a3-a40a-4adb-8336-d49f61546158",
                "name" => "Radio einschalten",
                "type" => "action",
                "model_id" => "ae7d1755-45f6-4cdd-845d-50b055e42752",
                "parent" => "55afa3c0-d907-42bb-8ece-e489df25494f",
                "status_type_id" => "681b27fb-24e4-46d8-b021-0bc52c66960c"
            ],
            "position" => [
                "x" => 911.11706737426,
                "y" => 480.82007563998
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "66c4edc2-5a62-4bf0-9938-9ee046ad7cef",
                "name" => "Radio ausschalten",
                "type" => "action",
                "model_id" => "933e6dd2-8a70-41b2-87dc-9c53685b37b6",
                "parent" => "55afa3c0-d907-42bb-8ece-e489df25494f",
                "status_type_id" => "681b27fb-24e4-46d8-b021-0bc52c66960c"
            ],
            "position" => [
                "x" => 1068.9986592093,
                "y" => 470.61717265088
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "011865b0-f32a-44e7-b522-0d7a909c5956",
                "parent" => "55afa3c0-d907-42bb-8ece-e489df25494f",
                "name" => "Radio ist ausgeschaltet",
                "model_id" => "a0ebf677-374d-475e-b6c7-d2980944244b",
                "status_type_id" => "681b27fb-24e4-46d8-b021-0bc52c66960c",
                "type" => "state"
            ],
            "position" => [
                "x" => 986.13638591894,
                "y" => 628.30320195714
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "2ca4cc0c-cf97-4b34-b8d5-3a6137f77011",
                "parent" => "55afa3c0-d907-42bb-8ece-e489df25494f",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 1018.6831806143,
                "y" => 297.43840417016
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-one"
        ],
        [
            "data" => [
                "id" => "17d47030-2aa6-4956-a62c-6867824d620c",
                "parent" => "2ca4cc0c-cf97-4b34-b8d5-3a6137f77011",
                "name" => "Radio ist eingeschaltet - leise Lautstärke",
                "model_id" => "1f60e980-4dea-4986-8356-17eaf495dbda",
                "status_type_id" => "681b27fb-24e4-46d8-b021-0bc52c66960c",
                "type" => "state"
            ],
            "position" => [
                "x" => 911.16039341644,
                "y" => 329.94656184313
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "29aee709-8d92-4c6c-9da9-b842822c20a7",
                "parent" => "2ca4cc0c-cf97-4b34-b8d5-3a6137f77011",
                "name" => "Radio ist eingeschaltet - mittlere Lautstärke",
                "model_id" => "2b10aff5-ed73-41fe-a336-aac2773464de",
                "status_type_id" => "681b27fb-24e4-46d8-b021-0bc52c66960c",
                "type" => "state"
            ],
            "position" => [
                "x" => 1018.350866112,
                "y" => 329.43024649719
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "10782638-3f2b-4f05-b286-c14be4fb20a6",
                "parent" => "2ca4cc0c-cf97-4b34-b8d5-3a6137f77011",
                "name" => "Radio ist eingeschaltet - laute Lautstärke",
                "model_id" => "b7e3e82e-620d-4288-9be3-a0b7f817e544",
                "status_type_id" => "681b27fb-24e4-46d8-b021-0bc52c66960c",
                "type" => "state"
            ],
            "position" => [
                "x" => 1125.2059678121,
                "y" => 328.23435742188
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "5da82b9d-7bc1-44f0-9c43-8514eba8f6b7",
                "name" => "Start",
                "type" => "start"
            ],
            "position" => [
                "x" => 1102.9000174253,
                "y" => 845.96509583561
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "start"
        ],
        [
            "data" => [
                "id" => "50621296-0cbc-410d-963c-3c8a04809454",
                "name" => "Freie Aktionen",
                "type" => "liberal"
            ],
            "position" => [
                "x" => 855.84427056841,
                "y" => 780.45659298474
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => true,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "liberal"
        ],
        [
            "data" => [
                "id" => "973af91e-5ad0-441a-bfb0-3d89f8b6eb0c 081df5b2-e09e-435b-bc1a-67585a45aa6b",
                "source" => "973af91e-5ad0-441a-bfb0-3d89f8b6eb0c",
                "source_model_id" => "5c4fd52d-6d8f-4066-b019-084e27eacb8b",
                "target_model_id" => "ac2066a6-6473-40f0-a2d3-469aebaf7517",
                "target" => "081df5b2-e09e-435b-bc1a-67585a45aa6b",
                "type" => "action-rule-edge",
                "model_id" => "d1ccf171-9f50-4fc3-a00c-d67a9d9eb692"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "081df5b2-e09e-435b-bc1a-67585a45aa6b 95e9fa68-2a9b-4f24-88ab-b1b4bc6215ce",
                "source" => "081df5b2-e09e-435b-bc1a-67585a45aa6b",
                "source_model_id" => "ac2066a6-6473-40f0-a2d3-469aebaf7517",
                "target_model_id" => "2c4a606d-784d-427b-9ff5-d094b68bb49a",
                "target" => "95e9fa68-2a9b-4f24-88ab-b1b4bc6215ce",
                "type" => "status-rule-edge",
                "model_id" => "9e3a90eb-e5de-4037-bd8c-fa9272089d45"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "95e9fa68-2a9b-4f24-88ab-b1b4bc6215ce b96ad115-3798-4ea7-ac64-d7d91a3360c2",
                "source" => "95e9fa68-2a9b-4f24-88ab-b1b4bc6215ce",
                "source_model_id" => "2c4a606d-784d-427b-9ff5-d094b68bb49a",
                "target_model_id" => "86e87b47-d586-4821-b479-df22f605a31c",
                "target" => "b96ad115-3798-4ea7-ac64-d7d91a3360c2",
                "type" => "action-rule-edge",
                "model_id" => "ad27f379-9dde-40da-922f-dcbc7f890451"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "b96ad115-3798-4ea7-ac64-d7d91a3360c2 973af91e-5ad0-441a-bfb0-3d89f8b6eb0c",
                "source" => "b96ad115-3798-4ea7-ac64-d7d91a3360c2",
                "source_model_id" => "86e87b47-d586-4821-b479-df22f605a31c",
                "target_model_id" => "5c4fd52d-6d8f-4066-b019-084e27eacb8b",
                "target" => "973af91e-5ad0-441a-bfb0-3d89f8b6eb0c",
                "type" => "status-rule-edge",
                "model_id" => "a0861575-782f-4cad-9461-b951c585afba"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "95e9fa68-2a9b-4f24-88ab-b1b4bc6215ce 851191e3-d4a6-4051-bf98-64902ee37bf4",
                "source" => "95e9fa68-2a9b-4f24-88ab-b1b4bc6215ce",
                "source_model_id" => "2c4a606d-784d-427b-9ff5-d094b68bb49a",
                "target_model_id" => "b0e4565e-ab34-4796-9746-c84a55add3b5",
                "target" => "851191e3-d4a6-4051-bf98-64902ee37bf4",
                "type" => "action-rule-edge",
                "model_id" => "ad27f379-9dde-40da-922f-dcbc7f890451"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "95e9fa68-2a9b-4f24-88ab-b1b4bc6215ce b2cd9892-baab-45da-972d-4540046fedf4",
                "source" => "95e9fa68-2a9b-4f24-88ab-b1b4bc6215ce",
                "source_model_id" => "2c4a606d-784d-427b-9ff5-d094b68bb49a",
                "target_model_id" => "ae7d1755-45f6-4cdd-845d-50b055e42752",
                "target" => "b2cd9892-baab-45da-972d-4540046fedf4",
                "type" => "action-rule-edge",
                "model_id" => "fe9424f2-17d8-4304-be97-0918ec3ecf24"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "c423ff8a-0b77-42af-b71d-3ae26573bee9 84746af3-f2ae-44a7-a1af-f49579a08fdd",
                "source" => "c423ff8a-0b77-42af-b71d-3ae26573bee9",
                "source_model_id" => "11a4c2d7-9f62-40b5-b5f2-efa0ac204f6f",
                "target_model_id" => "86e87b47-d586-4821-b479-df22f605a31c",
                "target" => "84746af3-f2ae-44a7-a1af-f49579a08fdd",
                "type" => "action-rule-edge",
                "model_id" => "cffc1949-c1c9-42cc-9db5-38f6bfe1c491"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "c423ff8a-0b77-42af-b71d-3ae26573bee9 1f1b3b5c-52a2-480f-86ca-7267c1575e77",
                "source" => "c423ff8a-0b77-42af-b71d-3ae26573bee9",
                "source_model_id" => "11a4c2d7-9f62-40b5-b5f2-efa0ac204f6f",
                "target_model_id" => "b0e4565e-ab34-4796-9746-c84a55add3b5",
                "target" => "1f1b3b5c-52a2-480f-86ca-7267c1575e77",
                "type" => "action-rule-edge",
                "model_id" => "31c37863-3ec8-4d7f-b0ab-a6a807003b0c"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "732179bd-7315-45a2-962c-de321b9355dc 1f1b3b5c-52a2-480f-86ca-7267c1575e77",
                "source" => "732179bd-7315-45a2-962c-de321b9355dc",
                "source_model_id" => null,
                "target_model_id" => "b0e4565e-ab34-4796-9746-c84a55add3b5",
                "target" => "1f1b3b5c-52a2-480f-86ca-7267c1575e77",
                "type" => "action-rule-edge",
                "model_id" => "31c37863-3ec8-4d7f-b0ab-a6a807003b0c"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "1f1b3b5c-52a2-480f-86ca-7267c1575e77 6df1d35a-e52c-4341-a00c-501451d7145f",
                "source" => "1f1b3b5c-52a2-480f-86ca-7267c1575e77",
                "source_model_id" => "b0e4565e-ab34-4796-9746-c84a55add3b5",
                "target_model_id" => "a0861575-782f-4cad-9461-b951c585afba",
                "target" => "6df1d35a-e52c-4341-a00c-501451d7145f",
                "type" => "status-rule-edge",
                "model_id" => "a0861575-782f-4cad-9461-b951c585afba"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "e7cc89a9-ae78-465f-b619-e40d7482204a 9d15f1a2-af6d-4140-9653-d381dc45bfdb",
                "source" => "e7cc89a9-ae78-465f-b619-e40d7482204a",
                "source_model_id" => null,
                "target_model_id" => "aa3dad52-6f93-4d83-a94b-795be7073553",
                "target" => "9d15f1a2-af6d-4140-9653-d381dc45bfdb",
                "type" => "action-rule-edge",
                "model_id" => "8c06fad2-aebc-4e82-ac2b-1b0b5d00de2e"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "9d15f1a2-af6d-4140-9653-d381dc45bfdb 677b4b1d-1e80-4154-a529-530962d01570",
                "source" => "9d15f1a2-af6d-4140-9653-d381dc45bfdb",
                "source_model_id" => "aa3dad52-6f93-4d83-a94b-795be7073553",
                "target_model_id" => "ab503277-0484-4657-b5d1-28cb49d49c9b",
                "target" => "677b4b1d-1e80-4154-a529-530962d01570",
                "type" => "status-rule-edge",
                "model_id" => "ab503277-0484-4657-b5d1-28cb49d49c9b"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "e7cc89a9-ae78-465f-b619-e40d7482204a 74fa007e-337d-4c91-9a97-c5a6b81de365",
                "source" => "e7cc89a9-ae78-465f-b619-e40d7482204a",
                "source_model_id" => null,
                "target_model_id" => "89543f5f-eed7-4fbb-b3d8-8c02c591a1cc",
                "target" => "74fa007e-337d-4c91-9a97-c5a6b81de365",
                "type" => "action-rule-edge",
                "model_id" => "cd2f60a0-5523-4040-ad7c-db1b5555fb1c"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "74fa007e-337d-4c91-9a97-c5a6b81de365 c423ff8a-0b77-42af-b71d-3ae26573bee9",
                "source" => "74fa007e-337d-4c91-9a97-c5a6b81de365",
                "source_model_id" => "89543f5f-eed7-4fbb-b3d8-8c02c591a1cc",
                "target_model_id" => "11a4c2d7-9f62-40b5-b5f2-efa0ac204f6f",
                "target" => "c423ff8a-0b77-42af-b71d-3ae26573bee9",
                "type" => "status-rule-edge",
                "model_id" => "8497835c-30a4-44af-8f31-da7bacce2ba3"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "c423ff8a-0b77-42af-b71d-3ae26573bee9 d19f3a80-bd1b-480d-9bfa-7d316664e0a1",
                "source" => "c423ff8a-0b77-42af-b71d-3ae26573bee9",
                "source_model_id" => "11a4c2d7-9f62-40b5-b5f2-efa0ac204f6f",
                "target_model_id" => "2d163f43-76d6-44cc-935e-13fc30e8aaa9",
                "target" => "d19f3a80-bd1b-480d-9bfa-7d316664e0a1",
                "type" => "action-rule-edge",
                "model_id" => "b2d5013b-1fad-4a3a-a98e-94bf9dd2d600"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "540a157f-e222-48f7-8bff-cadbcb97bba2 488e1201-3917-489b-ae96-fbf6a209fdb7",
                "source" => "540a157f-e222-48f7-8bff-cadbcb97bba2",
                "source_model_id" => null,
                "target_model_id" => "ac2066a6-6473-40f0-a2d3-469aebaf7517",
                "target" => "488e1201-3917-489b-ae96-fbf6a209fdb7",
                "type" => "action-rule-edge",
                "model_id" => "cffc1949-c1c9-42cc-9db5-38f6bfe1c491"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "540a157f-e222-48f7-8bff-cadbcb97bba2 5860993b-f7a0-4f43-8c8b-ba31b0fbd9b7",
                "source" => "540a157f-e222-48f7-8bff-cadbcb97bba2",
                "source_model_id" => null,
                "target_model_id" => "b0e4565e-ab34-4796-9746-c84a55add3b5",
                "target" => "5860993b-f7a0-4f43-8c8b-ba31b0fbd9b7",
                "type" => "action-rule-edge",
                "model_id" => "cffc1949-c1c9-42cc-9db5-38f6bfe1c491"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "5860993b-f7a0-4f43-8c8b-ba31b0fbd9b7 aaba84a1-6b44-456b-a6ec-fd2363e9d05a",
                "source" => "5860993b-f7a0-4f43-8c8b-ba31b0fbd9b7",
                "source_model_id" => "b0e4565e-ab34-4796-9746-c84a55add3b5",
                "target_model_id" => "044be779-21f9-423c-b9fa-94966841e271",
                "target" => "aaba84a1-6b44-456b-a6ec-fd2363e9d05a",
                "type" => "status-rule-edge",
                "model_id" => "044be779-21f9-423c-b9fa-94966841e271"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "75bade5d-66d7-4dff-8070-da46c567e439 00dc4ced-7381-4e94-b3c2-d9c67c556419",
                "source" => "75bade5d-66d7-4dff-8070-da46c567e439",
                "source_model_id" => "f71e64c7-a967-4c86-86a5-60ebfe5eeb65",
                "target_model_id" => "2d163f43-76d6-44cc-935e-13fc30e8aaa9",
                "target" => "00dc4ced-7381-4e94-b3c2-d9c67c556419",
                "type" => "action-rule-edge",
                "model_id" => "ade9bca5-7235-4184-b834-496fba06aab4"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "677636ca-7fa3-4c9b-a0de-b636a64caa6d 00dc4ced-7381-4e94-b3c2-d9c67c556419",
                "source" => "677636ca-7fa3-4c9b-a0de-b636a64caa6d",
                "source_model_id" => null,
                "target_model_id" => "2d163f43-76d6-44cc-935e-13fc30e8aaa9",
                "target" => "00dc4ced-7381-4e94-b3c2-d9c67c556419",
                "type" => "action-rule-edge",
                "model_id" => "ade9bca5-7235-4184-b834-496fba06aab4"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "00dc4ced-7381-4e94-b3c2-d9c67c556419 faf49074-3211-49c8-b626-d96d72daf8e0",
                "source" => "00dc4ced-7381-4e94-b3c2-d9c67c556419",
                "source_model_id" => "2d163f43-76d6-44cc-935e-13fc30e8aaa9",
                "target_model_id" => "917d577f-cae6-498d-abd9-a876a2cbd113",
                "target" => "faf49074-3211-49c8-b626-d96d72daf8e0",
                "type" => "status-rule-edge",
                "model_id" => "490066c7-0afa-486b-b4f1-9ac22e33d9a6"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "253898e7-b6e9-4cd5-884e-32755baa6bac 011865b0-f32a-44e7-b522-0d7a909c5956",
                "source" => "253898e7-b6e9-4cd5-884e-32755baa6bac",
                "source_model_id" => "86e87b47-d586-4821-b479-df22f605a31c",
                "target_model_id" => "a0ebf677-374d-475e-b6c7-d2980944244b",
                "target" => "011865b0-f32a-44e7-b522-0d7a909c5956",
                "type" => "status-rule-edge",
                "model_id" => "044be779-21f9-423c-b9fa-94966841e271"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "011865b0-f32a-44e7-b522-0d7a909c5956 14d1d2a3-a40a-4adb-8336-d49f61546158",
                "source" => "011865b0-f32a-44e7-b522-0d7a909c5956",
                "source_model_id" => "a0ebf677-374d-475e-b6c7-d2980944244b",
                "target_model_id" => "ae7d1755-45f6-4cdd-845d-50b055e42752",
                "target" => "14d1d2a3-a40a-4adb-8336-d49f61546158",
                "type" => "action-rule-edge",
                "model_id" => "a0856d62-a5f5-4382-ab83-52c4ca0c9245"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "14d1d2a3-a40a-4adb-8336-d49f61546158 2ca4cc0c-cf97-4b34-b8d5-3a6137f77011",
                "source" => "14d1d2a3-a40a-4adb-8336-d49f61546158",
                "source_model_id" => "ae7d1755-45f6-4cdd-845d-50b055e42752",
                "target_model_id" => null,
                "target" => "2ca4cc0c-cf97-4b34-b8d5-3a6137f77011",
                "type" => "status-rule-edge",
                "model_id" => "85aae4a3-dd1a-4c85-9d78-9dc566308dab"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "2ca4cc0c-cf97-4b34-b8d5-3a6137f77011 66c4edc2-5a62-4bf0-9938-9ee046ad7cef",
                "source" => "2ca4cc0c-cf97-4b34-b8d5-3a6137f77011",
                "source_model_id" => null,
                "target_model_id" => "933e6dd2-8a70-41b2-87dc-9c53685b37b6",
                "target" => "66c4edc2-5a62-4bf0-9938-9ee046ad7cef",
                "type" => "action-rule-edge",
                "model_id" => "6f51e54f-124c-4384-910a-b7adc5949e56"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "66c4edc2-5a62-4bf0-9938-9ee046ad7cef 011865b0-f32a-44e7-b522-0d7a909c5956",
                "source" => "66c4edc2-5a62-4bf0-9938-9ee046ad7cef",
                "source_model_id" => "933e6dd2-8a70-41b2-87dc-9c53685b37b6",
                "target_model_id" => "a0ebf677-374d-475e-b6c7-d2980944244b",
                "target" => "011865b0-f32a-44e7-b522-0d7a909c5956",
                "type" => "status-rule-edge",
                "model_id" => "66d9b44f-02aa-445c-9021-b0a309e1b036"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "5da82b9d-7bc1-44f0-9c43-8514eba8f6b7 50621296-0cbc-410d-963c-3c8a04809454",
                "source" => "5da82b9d-7bc1-44f0-9c43-8514eba8f6b7",
                "source_model_id" => null,
                "target_model_id" => null,
                "target" => "50621296-0cbc-410d-963c-3c8a04809454",
                "type" => "liberal-node-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "5da82b9d-7bc1-44f0-9c43-8514eba8f6b7 973af91e-5ad0-441a-bfb0-3d89f8b6eb0c",
                "source" => "5da82b9d-7bc1-44f0-9c43-8514eba8f6b7",
                "source_model_id" => null,
                "target_model_id" => "5c4fd52d-6d8f-4066-b019-084e27eacb8b",
                "target" => "973af91e-5ad0-441a-bfb0-3d89f8b6eb0c",
                "type" => "initial-state-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "5da82b9d-7bc1-44f0-9c43-8514eba8f6b7 c423ff8a-0b77-42af-b71d-3ae26573bee9",
                "source" => "5da82b9d-7bc1-44f0-9c43-8514eba8f6b7",
                "source_model_id" => null,
                "target_model_id" => "11a4c2d7-9f62-40b5-b5f2-efa0ac204f6f",
                "target" => "c423ff8a-0b77-42af-b71d-3ae26573bee9",
                "type" => "initial-state-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "5da82b9d-7bc1-44f0-9c43-8514eba8f6b7 faf49074-3211-49c8-b626-d96d72daf8e0",
                "source" => "5da82b9d-7bc1-44f0-9c43-8514eba8f6b7",
                "source_model_id" => null,
                "target_model_id" => "917d577f-cae6-498d-abd9-a876a2cbd113",
                "target" => "faf49074-3211-49c8-b626-d96d72daf8e0",
                "type" => "initial-state-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "5da82b9d-7bc1-44f0-9c43-8514eba8f6b7 011865b0-f32a-44e7-b522-0d7a909c5956",
                "source" => "5da82b9d-7bc1-44f0-9c43-8514eba8f6b7",
                "source_model_id" => null,
                "target_model_id" => "a0ebf677-374d-475e-b6c7-d2980944244b",
                "target" => "011865b0-f32a-44e7-b522-0d7a909c5956",
                "type" => "initial-state-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ]
    ],
    'issue_calculated' => [
        [
            "data" => [
                "id" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "name" => "Hauptstatus",
                "type" => "status",
                "model_id" => "14a0a283-6585-4f68-975f-c432f790cb6d"
            ],
            "position" => [
                "x" => 697.61161238247,
                "y" => 1113.609758683
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "status"
        ],
        [
            "data" => [
                "id" => "c4ac9b59-5e17-45a3-a84d-cf2b5112d63c",
                "name" => "Issue erstellen",
                "type" => "action",
                "model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d"
            ],
            "position" => [
                "x" => 763.94828192133,
                "y" => 854.29445193166
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "a8f1e53e-bc0c-4ae0-bb34-23c33bfea01c",
                "name" => "Issue ändern",
                "type" => "action",
                "model_id" => "d08223d5-4f6d-448d-a4b1-1c101e7543db",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d"
            ],
            "position" => [
                "x" => 639.79534738419,
                "y" => 853.46382812105
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "b1d434f8-a97c-4b3e-b9f5-88189175d10f",
                "name" => "Umsetzung starten",
                "type" => "action",
                "model_id" => "bed746e6-2312-44fd-9c0a-fc7ac7ff0af6",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d"
            ],
            "position" => [
                "x" => 674.39781177364,
                "y" => 1101.4167389435
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "d1b57c0e-1c3d-49a7-8793-560b6b6424dd",
                "name" => "Umsetzung stoppen",
                "type" => "action",
                "model_id" => "a8cbb33a-564f-439d-bfa9-63e3c21b1bc4",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d"
            ],
            "position" => [
                "x" => 852.78953388435,
                "y" => 1104.6919238837
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "cf03abe9-14c4-4aca-b6e3-16f2f9c6d4c9",
                "name" => "Fortschritt erfassen",
                "type" => "action",
                "model_id" => "eafc8da3-5dfa-4b4d-93a9-c2eda5b67f70",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d"
            ],
            "position" => [
                "x" => 800.39727234068,
                "y" => 1404.653375379
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "8e29c3d5-d303-4aeb-b445-d72f5131f755",
                "name" => "Issue prüfen",
                "type" => "action",
                "model_id" => "a514ce4a-fff1-4c65-8db2-1f300a4ba89c",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d"
            ],
            "position" => [
                "x" => 539.65941352442,
                "y" => 1240.7696157473
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "2b5586cb-f4eb-416d-bded-2678500c866c",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "name" => "Neu erfasst",
                "model_id" => "5e78fbbb-1fa2-4cca-89c0-fb134fc6b747",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d",
                "type" => "state"
            ],
            "position" => [
                "x" => 867.25281173502,
                "y" => 843.06614198691
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "df47ba08-70d6-4021-be22-c3af31f1d1d5",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "name" => "Zurückgestellt",
                "model_id" => "01bdaed4-e2cb-46bd-888e-da1ae828334b",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d",
                "type" => "state"
            ],
            "position" => [
                "x" => 539.49378694562,
                "y" => 976.47241001978
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "afa86e70-bd91-4530-b682-4df02178bdec",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "name" => "Abgewiesen",
                "model_id" => "7c663aa9-0824-4c59-b715-140d531115ac",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d",
                "type" => "state"
            ],
            "position" => [
                "x" => 542.4465148468,
                "y" => 870.30326561138
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "c207be8a-48f9-4de2-b4a7-4ec72c941552",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "name" => "Bereit zum Test",
                "model_id" => "62515cf2-8879-4185-91c0-c9a26bb8c8bc",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d",
                "type" => "state"
            ],
            "position" => [
                "x" => 539.97041302992,
                "y" => 1397.2257873778
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "89e8a90a-98af-4493-8154-3f9e7f0f820f",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "name" => "Abgenommen",
                "model_id" => "4719487b-178c-42ef-9d1b-f755dcaa1f73",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d",
                "type" => "state"
            ],
            "position" => [
                "x" => 540.65117980461,
                "y" => 1108.2790253952
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "b97d61ed-7f36-4010-abda-77fffffb2a93",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 729.24258120129,
                "y" => 954.49312001828
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-one"
        ],
        [
            "data" => [
                "id" => "a9867d5d-fe09-4feb-87cb-3ba258da3fa3",
                "parent" => "b97d61ed-7f36-4010-abda-77fffffb2a93",
                "name" => "Definiert",
                "model_id" => "65205a08-4469-4657-8369-e532c37d7116",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d",
                "type" => "state"
            ],
            "position" => [
                "x" => 647.5975907089,
                "y" => 967.13024653661
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "e91b5da6-092f-4228-9ca0-2957b083cd8f",
                "parent" => "b97d61ed-7f36-4010-abda-77fffffb2a93",
                "name" => "Pausiert",
                "model_id" => "b875b485-ee51-4073-83d0-b7c9c1cf7305",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d",
                "type" => "state"
            ],
            "position" => [
                "x" => 811.38757169368,
                "y" => 962.35599349995
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "2d61ce42-f188-4d02-a700-22f41f7a9838",
                "parent" => "4c05aa73-7dd2-427b-b1a6-a0b730df42a3",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 735.74348756853,
                "y" => 1226.846841272
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-one"
        ],
        [
            "data" => [
                "id" => "bce4474c-23ce-48e7-8c11-b07669903a31",
                "parent" => "2d61ce42-f188-4d02-a700-22f41f7a9838",
                "name" => "In Bearbeitung",
                "model_id" => "9bfc7231-a926-487f-9836-3389fabe48a4",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d",
                "type" => "state"
            ],
            "position" => [
                "x" => 799.93872024112,
                "y" => 1235.8686262732
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "a7527eb5-5390-4705-b107-4029601f579f",
                "parent" => "2d61ce42-f188-4d02-a700-22f41f7a9838",
                "name" => "Bedarf Korrektur",
                "model_id" => "9d974683-75cc-4247-a9dd-ffc2783c68b6",
                "status_type_id" => "14a0a283-6585-4f68-975f-c432f790cb6d",
                "type" => "state"
            ],
            "position" => [
                "x" => 678.04825489595,
                "y" => 1238.3250562709
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "cbf586f4-227d-46b9-aed8-4dc1d3b5a2e0",
                "name" => "Kategorie",
                "type" => "status",
                "model_id" => "25a326a0-c837-4f3e-8f96-8ec1dcf013ed"
            ],
            "position" => [
                "x" => 993.64283091255,
                "y" => 71.12944886936
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "status"
        ],
        [
            "data" => [
                "id" => "58502e6f-7e62-4664-a88e-31214e38715f",
                "name" => "Issue erstellen",
                "type" => "action",
                "model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "parent" => "cbf586f4-227d-46b9-aed8-4dc1d3b5a2e0",
                "status_type_id" => "25a326a0-c837-4f3e-8f96-8ec1dcf013ed"
            ],
            "position" => [
                "x" => 914.34054636929,
                "y" => 129.54321327506
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "ea35391c-0648-43b9-894f-898295b4cd9d",
                "name" => "Issue ändern",
                "type" => "action",
                "model_id" => "d08223d5-4f6d-448d-a4b1-1c101e7543db",
                "parent" => "cbf586f4-227d-46b9-aed8-4dc1d3b5a2e0",
                "status_type_id" => "25a326a0-c837-4f3e-8f96-8ec1dcf013ed"
            ],
            "position" => [
                "x" => 917.17068437997,
                "y" => 17.220518649893
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "fb8e0c1c-725b-4c20-b78a-8d26b10d6dd6",
                "parent" => "cbf586f4-227d-46b9-aed8-4dc1d3b5a2e0",
                "name" => "Nicht definiert",
                "model_id" => "a612b138-ec6f-420f-9308-b179a29193cb",
                "status_type_id" => "25a326a0-c837-4f3e-8f96-8ec1dcf013ed",
                "type" => "state"
            ],
            "position" => [
                "x" => 993.55607333012,
                "y" => 228.90842494132
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "e24fb5ac-60fc-476f-974a-795216eaefce",
                "parent" => "cbf586f4-227d-46b9-aed8-4dc1d3b5a2e0",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 1057.4451154558,
                "y" => 28.23405657384
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-one"
        ],
        [
            "data" => [
                "id" => "5e8f2013-1d00-4a82-ac6f-33783a305438",
                "parent" => "e24fb5ac-60fc-476f-974a-795216eaefce",
                "name" => "Anforderung",
                "model_id" => "49536f9b-51d3-4549-bdd5-d4623f68c7a9",
                "status_type_id" => "25a326a0-c837-4f3e-8f96-8ec1dcf013ed",
                "type" => "state"
            ],
            "position" => [
                "x" => 1057.4451154558,
                "y" => -44.649527202599
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "ccc8876a-ecd7-432f-a42d-2cba6b22f8b3",
                "parent" => "e24fb5ac-60fc-476f-974a-795216eaefce",
                "name" => "Bug",
                "model_id" => "4a0fea9e-6751-4621-89fe-36f792f77bed",
                "status_type_id" => "25a326a0-c837-4f3e-8f96-8ec1dcf013ed",
                "type" => "state"
            ],
            "position" => [
                "x" => 1061.0266584576,
                "y" => 121.61764035028
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "9d8bda86-005e-43ef-9b73-617b79b6d11a",
                "parent" => "e24fb5ac-60fc-476f-974a-795216eaefce",
                "name" => "Sonstige",
                "model_id" => "00b9693f-af2f-4c25-b71c-019445a6ef61",
                "status_type_id" => "25a326a0-c837-4f3e-8f96-8ec1dcf013ed",
                "type" => "state"
            ],
            "position" => [
                "x" => 1058.5927721825,
                "y" => 42.258161907717
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "b7340edc-77a7-4fa0-8812-bcb8f336dcfd",
                "name" => "Priorität",
                "type" => "status",
                "model_id" => "4e6f0c89-e89e-46aa-8231-0362721d8758"
            ],
            "position" => [
                "x" => 1316.4002187179,
                "y" => 67.765711000058
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "status"
        ],
        [
            "data" => [
                "id" => "d4d61732-f65b-488b-b97b-0c6a7659b31c",
                "name" => "Issue erstellen",
                "type" => "action",
                "model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "parent" => "b7340edc-77a7-4fa0-8812-bcb8f336dcfd",
                "status_type_id" => "4e6f0c89-e89e-46aa-8231-0362721d8758"
            ],
            "position" => [
                "x" => 1243.6675950242,
                "y" => 126.24392667146
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "d2f0172c-15d3-42a1-82d7-f9895347fc61",
                "name" => "Issue ändern",
                "type" => "action",
                "model_id" => "d08223d5-4f6d-448d-a4b1-1c101e7543db",
                "parent" => "b7340edc-77a7-4fa0-8812-bcb8f336dcfd",
                "status_type_id" => "4e6f0c89-e89e-46aa-8231-0362721d8758"
            ],
            "position" => [
                "x" => 1246.1958493708,
                "y" => 7.6711447283431
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "0646164c-5fca-48d9-b64e-24342b7d0206",
                "parent" => "b7340edc-77a7-4fa0-8812-bcb8f336dcfd",
                "name" => "Nicht definiert",
                "model_id" => "d0f2a0ed-83c9-426a-8a7a-9636ab3e4eb2",
                "status_type_id" => "4e6f0c89-e89e-46aa-8231-0362721d8758",
                "type" => "state"
            ],
            "position" => [
                "x" => 1232.6363640012,
                "y" => 224.99640945759
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "263dd752-c707-431f-90f9-8d864d63eaea",
                "parent" => "b7340edc-77a7-4fa0-8812-bcb8f336dcfd",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 1399.6390731658,
                "y" => 61.019553717638
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-one"
        ],
        [
            "data" => [
                "id" => "d99c9bbb-6aba-4737-ae5b-6a1e46643f71",
                "parent" => "263dd752-c707-431f-90f9-8d864d63eaea",
                "name" => "Keine",
                "model_id" => "e110659c-5208-4ceb-ac15-9478c2277cf6",
                "status_type_id" => "4e6f0c89-e89e-46aa-8231-0362721d8758",
                "type" => "state"
            ],
            "position" => [
                "x" => 1399.1949910844,
                "y" => -47.464987457475
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "2f91e55a-911e-47c4-ab47-f55366989eb2",
                "parent" => "263dd752-c707-431f-90f9-8d864d63eaea",
                "name" => "Niedrig",
                "model_id" => "aaef88d2-ca3d-45b8-bcee-1adc257908bd",
                "status_type_id" => "4e6f0c89-e89e-46aa-8231-0362721d8758",
                "type" => "state"
            ],
            "position" => [
                "x" => 1399.6140728969,
                "y" => 35.054095430452
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "be6d196d-dba7-4c79-8c4f-a1924380bef9",
                "parent" => "263dd752-c707-431f-90f9-8d864d63eaea",
                "name" => "Mittel",
                "model_id" => "fe32f6b1-017e-4651-8e69-187f3d3b29e3",
                "status_type_id" => "4e6f0c89-e89e-46aa-8231-0362721d8758",
                "type" => "state"
            ],
            "position" => [
                "x" => 1400.6640734346,
                "y" => 112.58501308021
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "0a7eb5b3-7497-4bbe-919e-eb1ca8d85b96",
                "parent" => "263dd752-c707-431f-90f9-8d864d63eaea",
                "name" => "Hoch",
                "model_id" => "f48f9387-8602-4082-b970-22b722ec878f",
                "status_type_id" => "4e6f0c89-e89e-46aa-8231-0362721d8758",
                "type" => "state"
            ],
            "position" => [
                "x" => 1400.1449905467,
                "y" => 190.00409489275
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "46d5b356-994f-404e-a031-6c3723455e44",
                "name" => "Zieltermin",
                "type" => "status",
                "model_id" => "2114935e-141e-4586-8d6c-86e064c35154"
            ],
            "position" => [
                "x" => 1520.9352731112,
                "y" => 832.61082396522
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "status"
        ],
        [
            "data" => [
                "id" => "e3f92b34-d208-4f0a-a09b-adeb689ff13f",
                "name" => "Issue erstellen",
                "type" => "action",
                "model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "parent" => "46d5b356-994f-404e-a031-6c3723455e44",
                "status_type_id" => "2114935e-141e-4586-8d6c-86e064c35154"
            ],
            "position" => [
                "x" => 1631.6131061747,
                "y" => 919.63746594533
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "64bde766-224a-4c9c-9b49-861f19f10493",
                "name" => "Umsetzung starten",
                "type" => "action",
                "model_id" => "bed746e6-2312-44fd-9c0a-fc7ac7ff0af6",
                "parent" => "46d5b356-994f-404e-a031-6c3723455e44",
                "status_type_id" => "2114935e-141e-4586-8d6c-86e064c35154"
            ],
            "position" => [
                "x" => 1529.8640810648,
                "y" => 800.94510129028
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "d912280b-1181-4d0f-99b0-022f6492f154",
                "parent" => "46d5b356-994f-404e-a031-6c3723455e44",
                "name" => "Nicht gesetzt",
                "model_id" => "c0223937-afaf-4f9f-8694-3e895e3ec465",
                "status_type_id" => "2114935e-141e-4586-8d6c-86e064c35154",
                "type" => "state"
            ],
            "position" => [
                "x" => 1404.7574400477,
                "y" => 766.08418198512
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "55d73e14-0dbc-435f-b0e0-39c8ccf6bbb3",
                "parent" => "46d5b356-994f-404e-a031-6c3723455e44",
                "name" => "[[process.data.deadline]]",
                "model_id" => "741e3a1a-33a2-415d-b420-b1413153033f",
                "status_type_id" => "2114935e-141e-4586-8d6c-86e064c35154",
                "type" => "state"
            ],
            "position" => [
                "x" => 1468.2779340912,
                "y" => 915.67663261432
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "3ff35849-709a-4541-a733-289c55a6e37d",
                "name" => "Aufwand",
                "type" => "status",
                "model_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9"
            ],
            "position" => [
                "x" => 1552.0012058352,
                "y" => 481.84760507904
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "status"
        ],
        [
            "data" => [
                "id" => "bf2f18a7-93a5-42f4-9c87-0d250e765413",
                "name" => "Issue erstellen",
                "type" => "action",
                "model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "parent" => "3ff35849-709a-4541-a733-289c55a6e37d",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9"
            ],
            "position" => [
                "x" => 1437.1930911453,
                "y" => 416.46833031362
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "2e93ca8a-1ba2-4c35-ab79-7714ce442a50",
                "name" => "Issue ändern",
                "type" => "action",
                "model_id" => "d08223d5-4f6d-448d-a4b1-1c101e7543db",
                "parent" => "3ff35849-709a-4541-a733-289c55a6e37d",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9"
            ],
            "position" => [
                "x" => 1433.4375796882,
                "y" => 608.98431027312
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => true,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "737f6a3c-7e0d-42a1-8108-f6d3b0caf61e",
                "name" => "Umsetzung starten",
                "type" => "action",
                "model_id" => "bed746e6-2312-44fd-9c0a-fc7ac7ff0af6",
                "parent" => "3ff35849-709a-4541-a733-289c55a6e37d",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9"
            ],
            "position" => [
                "x" => 1442.5232460763,
                "y" => 524.69540378093
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "3646b803-7bf3-4dab-9059-e1bf47665c50",
                "parent" => "3ff35849-709a-4541-a733-289c55a6e37d",
                "name" => "Nicht definiert",
                "model_id" => "371f3579-4f32-4640-bbaa-5f5aa75beb7f",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9",
                "type" => "state"
            ],
            "position" => [
                "x" => 1326.4509819578,
                "y" => 422.69011692112
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "parent" => "3ff35849-709a-4541-a733-289c55a6e37d",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 1674.5570060731,
                "y" => 480.31905578923
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-one"
        ],
        [
            "data" => [
                "id" => "4e3d0da3-83c9-44ee-bc72-7bf32cee98f2",
                "parent" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "name" => "~1 Stunde",
                "model_id" => "39963e16-8c61-41cf-91ee-840a8ea7a9e0",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9",
                "type" => "state"
            ],
            "position" => [
                "x" => 1762.5187678005,
                "y" => 502.03013037907
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "cea0f625-b045-4999-8459-768ae0c047ca",
                "parent" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "name" => "~4 Stunden",
                "model_id" => "f96847ff-969f-48c9-bcea-a433c574d644",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9",
                "type" => "state"
            ],
            "position" => [
                "x" => 1586.5625824335,
                "y" => 584.42721169351
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "839116f8-5b8c-4a86-bbde-0725ccd3cf17",
                "parent" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "name" => "~1 Tag",
                "model_id" => "91d6e016-756f-4c48-ab68-49562f740568",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9",
                "type" => "state"
            ],
            "position" => [
                "x" => 1670.4289983954,
                "y" => 503.85518669743
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "ccdeea38-29dc-4b89-a577-c6b644896c0b",
                "parent" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "name" => "~2 Tage",
                "model_id" => "626387d8-e1b8-4685-b7ca-2e83a31c9a31",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9",
                "type" => "state"
            ],
            "position" => [
                "x" => 1581.8722832249,
                "y" => 404.68038832418
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "66241cc7-5678-4786-bfab-4f5f2333162a",
                "parent" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "name" => "~3 Tage",
                "model_id" => "bcf1bdf5-0039-4058-b2cb-29db7321a88c",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9",
                "type" => "state"
            ],
            "position" => [
                "x" => 1669.7708848201,
                "y" => 582.92701820006
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "a34acbd2-ebc1-4cc5-b35d-e5924dc2aed4",
                "parent" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "name" => "~1 Woche",
                "model_id" => "3d3ab0fd-80ff-4c50-81bb-557d45a2fe11",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9",
                "type" => "state"
            ],
            "position" => [
                "x" => 1586.3758713034,
                "y" => 500.44062220643
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "1ab78e0b-8924-4493-aa71-83b2daac7776",
                "parent" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "name" => "~1.5 Woche",
                "model_id" => "2655174e-eaa0-4351-8c2d-bafbc98735a0",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9",
                "type" => "state"
            ],
            "position" => [
                "x" => 1761.0514297127,
                "y" => 396.71089988496
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "49bd67d1-d614-4549-be6d-e15411879bd6",
                "parent" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "name" => "~2 Wochen",
                "model_id" => "715bab1b-6835-4d4a-bdcc-8b3c183dd54b",
                "status_type_id" => "93c8edef-c55e-4d23-aab6-16ae26bbe0e9",
                "type" => "state"
            ],
            "position" => [
                "x" => 1666.3914650491,
                "y" => 401.88454348587
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "name" => "Aufwand IST",
                "type" => "status",
                "model_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130"
            ],
            "position" => [
                "x" => 641.25928826953,
                "y" => 549.65536891962
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "status"
        ],
        [
            "data" => [
                "id" => "38d5791b-240c-4ed6-bb3a-fe11a21ff078",
                "name" => "Fortschritt erfassen",
                "type" => "action",
                "model_id" => "eafc8da3-5dfa-4b4d-93a9-c2eda5b67f70",
                "parent" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "status_type_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130"
            ],
            "position" => [
                "x" => 778.64440697151,
                "y" => 614.3037385123
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "3d08f473-a475-478a-8c23-9dd99dfa97b2",
                "parent" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "name" => "Nicht gesetzt",
                "model_id" => "6f875ced-ef30-4d42-943b-7d349f55d8aa",
                "status_type_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130",
                "type" => "state"
            ],
            "position" => [
                "x" => 775.26970623659,
                "y" => 511.72740690079
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "17f48087-ef6f-4a90-909a-b8a3d9078acd",
                "parent" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "name" => "< 1 Stunde",
                "model_id" => "5001c3ec-0414-44ed-93ea-ee4efe8e46c3",
                "status_type_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130",
                "type" => "state"
            ],
            "position" => [
                "x" => 501.74887030247,
                "y" => 473.84397447336
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "c16ca0c1-d9e3-4b46-aa87-6970d117f7ed",
                "parent" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "name" => "< 4 Stunden",
                "model_id" => "fee1bc5e-6083-47b7-a050-8e3e432c7e3a",
                "status_type_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130",
                "type" => "state"
            ],
            "position" => [
                "x" => 599.16379498687,
                "y" => 474.18874071687
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "a65adb5d-9c4a-466d-9c97-77743606b7ea",
                "parent" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "name" => "< 1 Tag",
                "model_id" => "2c70ee1c-77d2-4c1c-95c7-29369a80975b",
                "status_type_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130",
                "type" => "state"
            ],
            "position" => [
                "x" => 691.11872440554,
                "y" => 473.46026109786
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "895d5c32-0842-4b70-819c-67acc868222a",
                "parent" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "name" => "2 Tage",
                "model_id" => "7255e82f-8f4a-4df1-b64a-0776b9144dda",
                "status_type_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130",
                "type" => "state"
            ],
            "position" => [
                "x" => 504.22150160117,
                "y" => 556.7845582697
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "cb62a212-4c78-4d6b-aeec-813328131406",
                "parent" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "name" => "3 Tage",
                "model_id" => "7d2188cb-23be-4874-892b-8e26b001d5bf",
                "status_type_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130",
                "type" => "state"
            ],
            "position" => [
                "x" => 601.79241169213,
                "y" => 557.75045452242
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "66b0c4a3-650e-45a2-97c1-c3431c07e1e1",
                "parent" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "name" => "4 Tage",
                "model_id" => "ba7fabe7-9076-464f-8b2e-caca18e21030",
                "status_type_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130",
                "type" => "state"
            ],
            "position" => [
                "x" => 686.62603874328,
                "y" => 554.69253276066
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "db1c4532-328f-416f-aacf-74ebd67861b7",
                "parent" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "name" => "5 Tage",
                "model_id" => "51d2842e-9ae0-4925-9192-5ca92abce1f0",
                "status_type_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130",
                "type" => "state"
            ],
            "position" => [
                "x" => 600.98110064337,
                "y" => 645.69612505075
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "0c5f9c80-67f5-45d6-9228-5075df7ca9c9",
                "parent" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "name" => "> 5 Tage",
                "model_id" => "0a65ccba-102e-41ec-9245-9dedaa79f94f",
                "status_type_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130",
                "type" => "state"
            ],
            "position" => [
                "x" => 502.36041754082,
                "y" => 646.35047674137
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state"
        ],
        [
            "data" => [
                "id" => "d86934a7-3b67-4098-b618-86873cab237a",
                "parent" => "48e15d4a-8fb0-46e2-b4d5-7d3709548831",
                "name" => "+ ?",
                "model_id" => "981e2339-0be5-468a-834c-f3b612c73cd1",
                "status_type_id" => "ef89d26b-4df7-4f5c-b9ed-0b0c54f57130",
                "action_type_id" => "eafc8da3-5dfa-4b4d-93a9-c2eda5b67f70",
                "type" => "statusrule-node"
            ],
            "position" => [
                "x" => 686.50990205448,
                "y" => 641.64374106886
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "statusrule-node"
        ],
        [
            "data" => [
                "id" => "729fb807-f8a3-42c7-b203-30e5bb5dd720",
                "name" => "Fortschritt der Umsetzung.",
                "type" => "status",
                "model_id" => "6c448ac8-2bea-425f-9d4f-6d15fd245d84"
            ],
            "position" => [
                "x" => 1127.9197473777,
                "y" => 961.72470950549
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "status"
        ],
        [
            "data" => [
                "id" => "c7a89b3c-fd09-4e97-955b-7c704917a5db",
                "name" => "Fortschritt erfassen",
                "type" => "action",
                "model_id" => "eafc8da3-5dfa-4b4d-93a9-c2eda5b67f70",
                "parent" => "729fb807-f8a3-42c7-b203-30e5bb5dd720",
                "status_type_id" => "6c448ac8-2bea-425f-9d4f-6d15fd245d84"
            ],
            "position" => [
                "x" => 1240.9971408956,
                "y" => 882.39775437041
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "f959f172-f0fd-45fe-a2b5-7fd2148cb7d0",
                "name" => "Issue prüfen",
                "type" => "action",
                "model_id" => "a514ce4a-fff1-4c65-8db2-1f300a4ba89c",
                "parent" => "729fb807-f8a3-42c7-b203-30e5bb5dd720",
                "status_type_id" => "6c448ac8-2bea-425f-9d4f-6d15fd245d84"
            ],
            "position" => [
                "x" => 1239.0647545277,
                "y" => 1030.0223514997
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "action"
        ],
        [
            "data" => [
                "id" => "a6d5d554-6c05-4eb5-ac97-0348fd24146f",
                "parent" => "729fb807-f8a3-42c7-b203-30e5bb5dd720",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 1079.9547617524,
                "y" => 961.72470950549
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-one"
        ],
        [
            "data" => [
                "id" => "986b44a2-56ab-4d27-a857-7797b534c94c",
                "parent" => "a6d5d554-6c05-4eb5-ac97-0348fd24146f",
                "name" => "",
                "type" => "compound"
            ],
            "position" => [
                "x" => 1079.9547617524,
                "y" => 1053.3465465245
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "compound compound-level-two"
        ],
        [
            "data" => [
                "id" => "2ee0e1c5-b438-4413-9d39-0780ed800e02",
                "parent" => "986b44a2-56ab-4d27-a857-7797b534c94c",
                "name" => "~80%",
                "model_id" => "b1d65ec3-67a5-4f57-8ca4-75310f2b9cd9",
                "status_type_id" => "6c448ac8-2bea-425f-9d4f-6d15fd245d84",
                "type" => "state"
            ],
            "position" => [
                "x" => 1118.1347832771,
                "y" => 1062.2219452734
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-two"
        ],
        [
            "data" => [
                "id" => "f8445189-44d3-4672-9667-b2d57130d6ce",
                "parent" => "986b44a2-56ab-4d27-a857-7797b534c94c",
                "name" => "100%",
                "model_id" => "11ed9d63-da9d-4861-b95a-1a388c7018cc",
                "status_type_id" => "6c448ac8-2bea-425f-9d4f-6d15fd245d84",
                "type" => "state"
            ],
            "position" => [
                "x" => 1041.7747402278,
                "y" => 1064.9711477756
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-two"
        ],
        [
            "data" => [
                "id" => "a5f19342-941c-4f02-bd2a-38454ddcc4d7",
                "parent" => "a6d5d554-6c05-4eb5-ac97-0348fd24146f",
                "name" => "0%",
                "model_id" => "e81e940c-3e36-462d-be80-c4abe319ca1a",
                "status_type_id" => "6c448ac8-2bea-425f-9d4f-6d15fd245d84",
                "type" => "state"
            ],
            "position" => [
                "x" => 1043.8584325359,
                "y" => 860.45807699428
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "905fd6c2-85b3-4f81-9de5-2d078e46849c",
                "parent" => "a6d5d554-6c05-4eb5-ac97-0348fd24146f",
                "name" => "~20%",
                "model_id" => "dbdb38df-207c-457e-bf53-087b58a56eb3",
                "status_type_id" => "6c448ac8-2bea-425f-9d4f-6d15fd245d84",
                "type" => "state"
            ],
            "position" => [
                "x" => 1116.6943789724,
                "y" => 857.47827123539
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "743b25a9-378f-4294-80f2-caf20dae782b",
                "parent" => "a6d5d554-6c05-4eb5-ac97-0348fd24146f",
                "name" => "~40%",
                "model_id" => "47f9fa43-f2d4-4d47-bfff-fbf9c8cd60f4",
                "status_type_id" => "6c448ac8-2bea-425f-9d4f-6d15fd245d84",
                "type" => "state"
            ],
            "position" => [
                "x" => 1041.7442335986,
                "y" => 938.61444789219
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "04fc8985-8eed-4bfd-b5b7-eaaec607e3ae",
                "parent" => "a6d5d554-6c05-4eb5-ac97-0348fd24146f",
                "name" => "~60%",
                "model_id" => "820c2bc5-f25e-441a-8844-832fe722f7f0",
                "status_type_id" => "6c448ac8-2bea-425f-9d4f-6d15fd245d84",
                "type" => "state"
            ],
            "position" => [
                "x" => 1119.9688719057,
                "y" => 939.32841117858
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "state state-level-one"
        ],
        [
            "data" => [
                "id" => "3f57db25-f219-4fea-b54d-247a9a3fa628",
                "name" => "Start",
                "type" => "start"
            ],
            "position" => [
                "x" => 1011.7598660077,
                "y" => 420.53830279973
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "start"
        ],
        [
            "data" => [
                "id" => "de538c5c-3449-4731-9df6-5d02d15fb5c5",
                "name" => "Freie Aktionen",
                "type" => "liberal"
            ],
            "position" => [
                "x" => 736.87852853936,
                "y" => 279.1461669919
            ],
            "group" => "nodes",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => false,
            "classes" => "liberal"
        ],
        [
            "data" => [
                "id" => "2b5586cb-f4eb-416d-bded-2678500c866c c4ac9b59-5e17-45a3-a84d-cf2b5112d63c",
                "source" => "2b5586cb-f4eb-416d-bded-2678500c866c",
                "source_model_id" => "5e78fbbb-1fa2-4cca-89c0-fb134fc6b747",
                "target_model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "target" => "c4ac9b59-5e17-45a3-a84d-cf2b5112d63c",
                "type" => "action-rule-edge",
                "model_id" => "39eb51f7-325f-4948-b114-fa74d1ea8525"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "c4ac9b59-5e17-45a3-a84d-cf2b5112d63c a9867d5d-fe09-4feb-87cb-3ba258da3fa3",
                "source" => "c4ac9b59-5e17-45a3-a84d-cf2b5112d63c",
                "source_model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "target_model_id" => "65205a08-4469-4657-8369-e532c37d7116",
                "target" => "a9867d5d-fe09-4feb-87cb-3ba258da3fa3",
                "type" => "status-rule-edge",
                "model_id" => "0649fecd-2bee-4a45-8c55-0a9e66c930a2"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "b97d61ed-7f36-4010-abda-77fffffb2a93 a8f1e53e-bc0c-4ae0-bb34-23c33bfea01c",
                "source" => "b97d61ed-7f36-4010-abda-77fffffb2a93",
                "source_model_id" => null,
                "target_model_id" => "d08223d5-4f6d-448d-a4b1-1c101e7543db",
                "target" => "a8f1e53e-bc0c-4ae0-bb34-23c33bfea01c",
                "type" => "action-rule-edge",
                "model_id" => "0d05c7df-4107-4cb3-a044-8e754721fd15"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "a8f1e53e-bc0c-4ae0-bb34-23c33bfea01c a9867d5d-fe09-4feb-87cb-3ba258da3fa3",
                "source" => "a8f1e53e-bc0c-4ae0-bb34-23c33bfea01c",
                "source_model_id" => "d08223d5-4f6d-448d-a4b1-1c101e7543db",
                "target_model_id" => "65205a08-4469-4657-8369-e532c37d7116",
                "target" => "a9867d5d-fe09-4feb-87cb-3ba258da3fa3",
                "type" => "status-rule-edge",
                "model_id" => "ce3aaa5d-c58b-4748-9dd2-2e66ce2935e4"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "b97d61ed-7f36-4010-abda-77fffffb2a93 b1d434f8-a97c-4b3e-b9f5-88189175d10f",
                "source" => "b97d61ed-7f36-4010-abda-77fffffb2a93",
                "source_model_id" => null,
                "target_model_id" => "bed746e6-2312-44fd-9c0a-fc7ac7ff0af6",
                "target" => "b1d434f8-a97c-4b3e-b9f5-88189175d10f",
                "type" => "action-rule-edge",
                "model_id" => "a2a6e6d9-5673-4e16-8dbc-26f11a0635e0"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "b1d434f8-a97c-4b3e-b9f5-88189175d10f bce4474c-23ce-48e7-8c11-b07669903a31",
                "source" => "b1d434f8-a97c-4b3e-b9f5-88189175d10f",
                "source_model_id" => "bed746e6-2312-44fd-9c0a-fc7ac7ff0af6",
                "target_model_id" => "9bfc7231-a926-487f-9836-3389fabe48a4",
                "target" => "bce4474c-23ce-48e7-8c11-b07669903a31",
                "type" => "status-rule-edge",
                "model_id" => "fdb6b7ce-3b4e-4362-9d6f-3a20ef34d05e"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "2d61ce42-f188-4d02-a700-22f41f7a9838 d1b57c0e-1c3d-49a7-8793-560b6b6424dd",
                "source" => "2d61ce42-f188-4d02-a700-22f41f7a9838",
                "source_model_id" => null,
                "target_model_id" => "a8cbb33a-564f-439d-bfa9-63e3c21b1bc4",
                "target" => "d1b57c0e-1c3d-49a7-8793-560b6b6424dd",
                "type" => "action-rule-edge",
                "model_id" => "faee515a-5e03-4346-943c-0598f91e5587"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "d1b57c0e-1c3d-49a7-8793-560b6b6424dd e91b5da6-092f-4228-9ca0-2957b083cd8f",
                "source" => "d1b57c0e-1c3d-49a7-8793-560b6b6424dd",
                "source_model_id" => "a8cbb33a-564f-439d-bfa9-63e3c21b1bc4",
                "target_model_id" => "b875b485-ee51-4073-83d0-b7c9c1cf7305",
                "target" => "e91b5da6-092f-4228-9ca0-2957b083cd8f",
                "type" => "status-rule-edge",
                "model_id" => "0c502555-fdc6-4ddf-b891-2a2b7fe7a16d"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "2d61ce42-f188-4d02-a700-22f41f7a9838 cf03abe9-14c4-4aca-b6e3-16f2f9c6d4c9",
                "source" => "2d61ce42-f188-4d02-a700-22f41f7a9838",
                "source_model_id" => null,
                "target_model_id" => "eafc8da3-5dfa-4b4d-93a9-c2eda5b67f70",
                "target" => "cf03abe9-14c4-4aca-b6e3-16f2f9c6d4c9",
                "type" => "action-rule-edge",
                "model_id" => "3a0db54d-c4a4-48d7-9f38-77bebbfc3dee"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "cf03abe9-14c4-4aca-b6e3-16f2f9c6d4c9 c207be8a-48f9-4de2-b4a7-4ec72c941552",
                "source" => "cf03abe9-14c4-4aca-b6e3-16f2f9c6d4c9",
                "source_model_id" => "eafc8da3-5dfa-4b4d-93a9-c2eda5b67f70",
                "target_model_id" => "62515cf2-8879-4185-91c0-c9a26bb8c8bc",
                "target" => "c207be8a-48f9-4de2-b4a7-4ec72c941552",
                "type" => "status-rule-edge",
                "model_id" => "c5121977-81fc-4449-8c66-3ea4809c8837"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "cf03abe9-14c4-4aca-b6e3-16f2f9c6d4c9 bce4474c-23ce-48e7-8c11-b07669903a31",
                "source" => "cf03abe9-14c4-4aca-b6e3-16f2f9c6d4c9",
                "source_model_id" => "eafc8da3-5dfa-4b4d-93a9-c2eda5b67f70",
                "target_model_id" => "9bfc7231-a926-487f-9836-3389fabe48a4",
                "target" => "bce4474c-23ce-48e7-8c11-b07669903a31",
                "type" => "status-rule-edge",
                "model_id" => "c5121977-81fc-4449-8c66-3ea4809c8837"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "c207be8a-48f9-4de2-b4a7-4ec72c941552 8e29c3d5-d303-4aeb-b445-d72f5131f755",
                "source" => "c207be8a-48f9-4de2-b4a7-4ec72c941552",
                "source_model_id" => "62515cf2-8879-4185-91c0-c9a26bb8c8bc",
                "target_model_id" => "a514ce4a-fff1-4c65-8db2-1f300a4ba89c",
                "target" => "8e29c3d5-d303-4aeb-b445-d72f5131f755",
                "type" => "action-rule-edge",
                "model_id" => "660a7906-862d-4ad4-8589-840e79b34417"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "8e29c3d5-d303-4aeb-b445-d72f5131f755 89e8a90a-98af-4493-8154-3f9e7f0f820f",
                "source" => "8e29c3d5-d303-4aeb-b445-d72f5131f755",
                "source_model_id" => "a514ce4a-fff1-4c65-8db2-1f300a4ba89c",
                "target_model_id" => "4719487b-178c-42ef-9d1b-f755dcaa1f73",
                "target" => "89e8a90a-98af-4493-8154-3f9e7f0f820f",
                "type" => "status-rule-edge",
                "model_id" => "ed58a8ba-3b72-4505-8f28-3ef0c62cc2a5"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "8e29c3d5-d303-4aeb-b445-d72f5131f755 a7527eb5-5390-4705-b107-4029601f579f",
                "source" => "8e29c3d5-d303-4aeb-b445-d72f5131f755",
                "source_model_id" => "a514ce4a-fff1-4c65-8db2-1f300a4ba89c",
                "target_model_id" => "9d974683-75cc-4247-a9dd-ffc2783c68b6",
                "target" => "a7527eb5-5390-4705-b107-4029601f579f",
                "type" => "status-rule-edge",
                "model_id" => "ed58a8ba-3b72-4505-8f28-3ef0c62cc2a5"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "58502e6f-7e62-4664-a88e-31214e38715f e24fb5ac-60fc-476f-974a-795216eaefce",
                "source" => "58502e6f-7e62-4664-a88e-31214e38715f",
                "source_model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "target_model_id" => null,
                "target" => "e24fb5ac-60fc-476f-974a-795216eaefce",
                "type" => "status-rule-edge",
                "model_id" => "bbcdddaf-bb43-4089-9cd3-84a39a81e60b"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "ea35391c-0648-43b9-894f-898295b4cd9d e24fb5ac-60fc-476f-974a-795216eaefce",
                "source" => "ea35391c-0648-43b9-894f-898295b4cd9d",
                "source_model_id" => "d08223d5-4f6d-448d-a4b1-1c101e7543db",
                "target_model_id" => null,
                "target" => "e24fb5ac-60fc-476f-974a-795216eaefce",
                "type" => "status-rule-edge",
                "model_id" => "cab4b19d-2f43-422a-8ea3-966b80e898a2"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "d4d61732-f65b-488b-b97b-0c6a7659b31c 263dd752-c707-431f-90f9-8d864d63eaea",
                "source" => "d4d61732-f65b-488b-b97b-0c6a7659b31c",
                "source_model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "target_model_id" => null,
                "target" => "263dd752-c707-431f-90f9-8d864d63eaea",
                "type" => "status-rule-edge",
                "model_id" => "f8443ebc-b0a3-4f9b-b9a9-61c28fc34ef6"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "d2f0172c-15d3-42a1-82d7-f9895347fc61 263dd752-c707-431f-90f9-8d864d63eaea",
                "source" => "d2f0172c-15d3-42a1-82d7-f9895347fc61",
                "source_model_id" => "d08223d5-4f6d-448d-a4b1-1c101e7543db",
                "target_model_id" => null,
                "target" => "263dd752-c707-431f-90f9-8d864d63eaea",
                "type" => "status-rule-edge",
                "model_id" => "5a737ad2-8081-4875-accb-1db70c226f2d"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "e3f92b34-d208-4f0a-a09b-adeb689ff13f 55d73e14-0dbc-435f-b0e0-39c8ccf6bbb3",
                "source" => "e3f92b34-d208-4f0a-a09b-adeb689ff13f",
                "source_model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "target_model_id" => "741e3a1a-33a2-415d-b420-b1413153033f",
                "target" => "55d73e14-0dbc-435f-b0e0-39c8ccf6bbb3",
                "type" => "status-rule-edge",
                "model_id" => "aabaf350-a1fc-42ac-9863-8ef6b5d57f53"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "64bde766-224a-4c9c-9b49-861f19f10493 55d73e14-0dbc-435f-b0e0-39c8ccf6bbb3",
                "source" => "64bde766-224a-4c9c-9b49-861f19f10493",
                "source_model_id" => "bed746e6-2312-44fd-9c0a-fc7ac7ff0af6",
                "target_model_id" => "741e3a1a-33a2-415d-b420-b1413153033f",
                "target" => "55d73e14-0dbc-435f-b0e0-39c8ccf6bbb3",
                "type" => "status-rule-edge",
                "model_id" => "98461f97-b1ff-4978-82cb-49e293246599"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "bf2f18a7-93a5-42f4-9c87-0d250e765413 3646b803-7bf3-4dab-9059-e1bf47665c50",
                "source" => "bf2f18a7-93a5-42f4-9c87-0d250e765413",
                "source_model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "target_model_id" => "371f3579-4f32-4640-bbaa-5f5aa75beb7f",
                "target" => "3646b803-7bf3-4dab-9059-e1bf47665c50",
                "type" => "status-rule-edge",
                "model_id" => "543e5177-8f7c-4012-84e9-b00b55bef300"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "bf2f18a7-93a5-42f4-9c87-0d250e765413 87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "source" => "bf2f18a7-93a5-42f4-9c87-0d250e765413",
                "source_model_id" => "2783ae18-da52-4942-8b0c-363e43644f33",
                "target_model_id" => null,
                "target" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "type" => "status-rule-edge",
                "model_id" => "543e5177-8f7c-4012-84e9-b00b55bef300"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "2e93ca8a-1ba2-4c35-ab79-7714ce442a50 87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "source" => "2e93ca8a-1ba2-4c35-ab79-7714ce442a50",
                "source_model_id" => "d08223d5-4f6d-448d-a4b1-1c101e7543db",
                "target_model_id" => null,
                "target" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "type" => "status-rule-edge",
                "model_id" => "1f3f0725-9490-467b-97e5-9e9112b5ccca"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "737f6a3c-7e0d-42a1-8108-f6d3b0caf61e 87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "source" => "737f6a3c-7e0d-42a1-8108-f6d3b0caf61e",
                "source_model_id" => "bed746e6-2312-44fd-9c0a-fc7ac7ff0af6",
                "target_model_id" => null,
                "target" => "87bc84ac-6df2-4fdc-9dc5-feeac514a5dd",
                "type" => "status-rule-edge",
                "model_id" => "ea93777f-be96-462b-a292-1a936ecdbc4d"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "38d5791b-240c-4ed6-bb3a-fe11a21ff078 d86934a7-3b67-4098-b618-86873cab237a",
                "source" => "38d5791b-240c-4ed6-bb3a-fe11a21ff078",
                "source_model_id" => "eafc8da3-5dfa-4b4d-93a9-c2eda5b67f70",
                "target_model_id" => "981e2339-0be5-468a-834c-f3b612c73cd1",
                "target" => "d86934a7-3b67-4098-b618-86873cab237a",
                "type" => "status-rule-edge",
                "model_id" => "981e2339-0be5-468a-834c-f3b612c73cd1"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "c7a89b3c-fd09-4e97-955b-7c704917a5db a6d5d554-6c05-4eb5-ac97-0348fd24146f",
                "source" => "c7a89b3c-fd09-4e97-955b-7c704917a5db",
                "source_model_id" => "eafc8da3-5dfa-4b4d-93a9-c2eda5b67f70",
                "target_model_id" => null,
                "target" => "a6d5d554-6c05-4eb5-ac97-0348fd24146f",
                "type" => "status-rule-edge",
                "model_id" => "40165529-1e9c-4c25-8e6e-71fd2b0b90f9"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "f959f172-f0fd-45fe-a2b5-7fd2148cb7d0 986b44a2-56ab-4d27-a857-7797b534c94c",
                "source" => "f959f172-f0fd-45fe-a2b5-7fd2148cb7d0",
                "source_model_id" => "a514ce4a-fff1-4c65-8db2-1f300a4ba89c",
                "target_model_id" => null,
                "target" => "986b44a2-56ab-4d27-a857-7797b534c94c",
                "type" => "status-rule-edge",
                "model_id" => "72c235dd-d3e1-4e5c-a056-c9a169daea82"
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "3f57db25-f219-4fea-b54d-247a9a3fa628 de538c5c-3449-4731-9df6-5d02d15fb5c5",
                "source" => "3f57db25-f219-4fea-b54d-247a9a3fa628",
                "source_model_id" => null,
                "target_model_id" => null,
                "target" => "de538c5c-3449-4731-9df6-5d02d15fb5c5",
                "type" => "liberal-node-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "3f57db25-f219-4fea-b54d-247a9a3fa628 2b5586cb-f4eb-416d-bded-2678500c866c",
                "source" => "3f57db25-f219-4fea-b54d-247a9a3fa628",
                "source_model_id" => null,
                "target_model_id" => "5e78fbbb-1fa2-4cca-89c0-fb134fc6b747",
                "target" => "2b5586cb-f4eb-416d-bded-2678500c866c",
                "type" => "initial-state-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "3f57db25-f219-4fea-b54d-247a9a3fa628 fb8e0c1c-725b-4c20-b78a-8d26b10d6dd6",
                "source" => "3f57db25-f219-4fea-b54d-247a9a3fa628",
                "source_model_id" => null,
                "target_model_id" => "a612b138-ec6f-420f-9308-b179a29193cb",
                "target" => "fb8e0c1c-725b-4c20-b78a-8d26b10d6dd6",
                "type" => "initial-state-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "3f57db25-f219-4fea-b54d-247a9a3fa628 0646164c-5fca-48d9-b64e-24342b7d0206",
                "source" => "3f57db25-f219-4fea-b54d-247a9a3fa628",
                "source_model_id" => null,
                "target_model_id" => "d0f2a0ed-83c9-426a-8a7a-9636ab3e4eb2",
                "target" => "0646164c-5fca-48d9-b64e-24342b7d0206",
                "type" => "initial-state-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "3f57db25-f219-4fea-b54d-247a9a3fa628 d912280b-1181-4d0f-99b0-022f6492f154",
                "source" => "3f57db25-f219-4fea-b54d-247a9a3fa628",
                "source_model_id" => null,
                "target_model_id" => "c0223937-afaf-4f9f-8694-3e895e3ec465",
                "target" => "d912280b-1181-4d0f-99b0-022f6492f154",
                "type" => "initial-state-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "3f57db25-f219-4fea-b54d-247a9a3fa628 3646b803-7bf3-4dab-9059-e1bf47665c50",
                "source" => "3f57db25-f219-4fea-b54d-247a9a3fa628",
                "source_model_id" => null,
                "target_model_id" => "371f3579-4f32-4640-bbaa-5f5aa75beb7f",
                "target" => "3646b803-7bf3-4dab-9059-e1bf47665c50",
                "type" => "initial-state-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "3f57db25-f219-4fea-b54d-247a9a3fa628 3d08f473-a475-478a-8c23-9dd99dfa97b2",
                "source" => "3f57db25-f219-4fea-b54d-247a9a3fa628",
                "source_model_id" => null,
                "target_model_id" => "6f875ced-ef30-4d42-943b-7d349f55d8aa",
                "target" => "3d08f473-a475-478a-8c23-9dd99dfa97b2",
                "type" => "initial-state-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ],
        [
            "data" => [
                "id" => "3f57db25-f219-4fea-b54d-247a9a3fa628 a5f19342-941c-4f02-bd2a-38454ddcc4d7",
                "source" => "3f57db25-f219-4fea-b54d-247a9a3fa628",
                "source_model_id" => null,
                "target_model_id" => "e81e940c-3e36-462d-be80-c4abe319ca1a",
                "target" => "a5f19342-941c-4f02-bd2a-38454ddcc4d7",
                "type" => "initial-state-edge",
                "model_id" => null
            ],
            "position" => [
                "x" => 0,
                "y" => 0
            ],
            "group" => "edges",
            "removed" => false,
            "selected" => false,
            "selectable" => true,
            "locked" => false,
            "grabbable" => true,
            "pannable" => true,
            "classes" => "edge"
        ]
    ],
];
