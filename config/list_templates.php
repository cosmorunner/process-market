<?php

return [
    'processes' => [
        'name' => 'Alle Prozesse',
        'slug' => 'all',
        'description' => 'Alle Prozesse.',
        'template' => 'processes',
        'data' => [
            'limit_to_user_involvement' => false,
            'limit_to_user_involvement_roles' => [],
            'rows_per_page' => 10,
            'header_button' => [
                'label' => 'Neuer Prozess',
                'type' => 'link',
                'type_options' => [
                    'url' => '/processes/create',
                    'bindings' => [],
                    'target' => '_self',
                ]
            ],
            'enable_label' => true,
            'enable_search' => true,
            'enable_pagination' => true,
            'enable_total_count' => true,
            'quick_filter' => [],
            'source_type' => 'sql',
            'source' => [
                'from' => 'processes',
                'select' => [
                    'owner_processes.id as owner_processes_id',
                    "CONCAT('process|', processes.id, '[', processes.name, ']') as processes_pipe_notation",
                    'processes.id as processes_id',
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
                'leftJoin' => [
                    [
                        'table' => 'processes as owner_processes',
                        'on' => [
                            [
                                'owner_processes.id',
                                '=',
                                '[[process.meta.id]]'
                            ]
                        ]
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
                    'width' => 2,
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
                    'width' => 3,
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
    'users' => [
        'name' => 'Benutzer',
        'slug' => 'users',
        'description' => '',
        'template' => 'users',
        'data' => [
            'header_button' => [],
            'rows_per_page' => 10,
            'enable_label' => true,
            'enable_search' => true,
            'enable_pagination' => true,
            'enable_total_count' => true,
            'quick_filter' => [],
            'source_type' => 'sql_users',
            'source' => [
                'where' => [],
                'orderBy' => [
                    [
                        'users_first_name',
                        'asc'
                    ]
                ],
            ],
            'columns' => [
                [
                    'type' => 'text',
                    'data' => 'users_full_name',
                    'sort' => 0,
                    'type_options' => [],
                    'label' => 'Name',
                    'width' => 8,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
                [
                    'type' => 'tags',
                    'data' => 'users_tags',
                    'sort' => 1,
                    'type_options' => [],
                    'label' => 'Tags',
                    'width' => 4,
                    'searchable' => true,
                    'sortable' => false,
                    'color' => ''
                ],
            ],
        ],
    ],
    'groups' => [
        'name' => 'Gruppen',
        'slug' => 'groups',
        'description' => '',
        'template' => 'groups',
        'data' => [
            'header_button' => [],
            'rows_per_page' => 10,
            'enable_label' => true,
            'enable_search' => true,
            'enable_pagination' => true,
            'enable_total_count' => true,
            'quick_filter' => [],
            'source_type' => 'sql_groups',
            'source' => [
                'where' => [],
                'orderBy' => [
                    [
                        'groups_name',
                        'asc'
                    ]
                ],
            ],
            'columns' => [
                [
                    'type' => 'text',
                    'data' => 'groups_name',
                    'sort' => 0,
                    'type_options' => [],
                    'label' => 'app.name',
                    'width' => 8,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
                [
                    'type' => 'tags',
                    'data' => 'groups_tags',
                    'sort' => 1,
                    'type_options' => [],
                    'label' => 'Tags',
                    'width' => 4,
                    'searchable' => true,
                    'sortable' => false,
                    'color' => ''
                ],
            ],
        ],
    ],
    'group_members' => [
        'name' => 'Gruppen-Mitglieder',
        'slug' => 'group-members',
        'description' => 'Alle Gruppen-Mitglieder der gewählten Gruppe(n)',
        'template' => 'group_members',
        'data' => [
            'header_button' => [],
            'rows_per_page' => 10,
            'enable_label' => true,
            'enable_search' => true,
            'enable_pagination' => true,
            'enable_total_count' => true,
            'quick_filter' => [],
            'source_type' => 'accesses',
            'source' => [
                'type' => 'group_accesses',
                'items' => [],
                'select' => [
                    [
                        'data' => 'id',
                        'alias' => 'users_id'
                    ],
                    [
                        'data' => 'first_name',
                        'alias' => 'users_first_name'
                    ],
                    [
                        'data' => 'last_name',
                        'alias' => 'users_last_name'
                    ],
                    [
                        'data' => 'full_name',
                        'alias' => 'users_full_name'
                    ],
                    [
                        'data' => 'email',
                        'alias' => 'users_email'
                    ],
                    [
                        'data' => 'user_pipe_notation',
                        'alias' => 'users_pipe_notation'
                    ],
                    [
                        'data' => 'entry_date',
                        'alias' => 'users_group_entry_date'
                    ],
                    [
                        'data' => 'group_name',
                        'alias' => 'groups_name'
                    ],
                    [
                        'data' => 'group_pipe_notation',
                        'alias' => 'groups_pipe_notation'
                    ],
                    [
                        'data' => 'role_name',
                        'alias' => 'groups_role_name'
                    ],
                    [
                        'data' => 'role_pipe_notation',
                        'alias' => 'groups_role_pipe_notation'
                    ],
                ],
            ],
            'columns' => [
                [
                    'type' => 'text',
                    'data' => 'users_first_name',
                    'sort' => 0,
                    'type_options' => [],
                    'label' => 'Vorname',
                    'width' => 3,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
                [
                    'type' => 'text',
                    'data' => 'users_last_name',
                    'sort' => 1,
                    'type_options' => [],
                    'label' => 'Nachname',
                    'width' => 3,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
                [
                    'type' => 'text',
                    'data' => 'groups_role_name',
                    'sort' => 2,
                    'type_options' => [],
                    'label' => 'Rollen-Name',
                    'width' => 3,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
                [
                    'type' => 'text',
                    'data' => 'users_group_entry_date',
                    'sort' => 3,
                    'type_options' => [
                        'date_format' => 'd.m.Y H:i'
                    ],
                    'label' => 'Beitrittsdatum',
                    'width' => 3,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
            ],
        ],
    ],
    'process_actions' => [
        'name' => 'Aktionen',
        'slug' => 'actions',
        'description' => 'Alle Aktionen, die in diesem Prozess ausgeführt wurden.',
        'template' => 'process_actions',
        'data' => [
            'limit_to_user_involvement' => false,
            'limit_to_user_involvement_roles' => [],
            'rows_per_page' => 10,
            'enable_label' => true,
            'enable_search' => true,
            'enable_pagination' => true,
            'enable_total_count' => true,
            'quick_filter' => [],
            'source_type' => 'sql_actions',
            'source' => [
                'select' => [],
                'where' => [],
                'orderBy' => [
                    [
                        'actions_created_at',
                        'desc'
                    ]
                ],
                'whereIn' => []
            ],
            'columns' => [
                [
                    'type' => 'url',
                    'data' => 'actions_action_type_name',
                    'sort' => 0,
                    'type_options' => [
                        'url' => '/actions/?',
                        'bindings' => [
                            'actions_id'
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
                    'data' => 'processes_name',
                    'sort' => 1,
                    'type_options' => [],
                    'label' => 'Author',
                    'width' => 4,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
                [
                    'type' => 'text',
                    'data' => 'actions_created_at',
                    'sort' => 2,
                    'type_options' => [
                        'date_format' => 'diffForHumans'
                    ],
                    'label' => 'Ausgeführt am',
                    'width' => 4,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ]
            ],
        ]
    ],
    'process_artifacts' => [
        'name' => 'Dokumente',
        'slug' => 'documents',
        'description' => 'Alle Dokumente des Prozesses.',
        'template' => 'process_artifacts',
        'data' => [
            'limit_to_user_involvement' => false,
            'limit_to_user_involvement_roles' => [],
            'rows_per_page' => 10,
            'enable_label' => true,
            'enable_search' => true,
            'enable_pagination' => true,
            'enable_total_count' => true,
            'quick_filter' => [],
            'additional_filters' => [
                [
                    'type' => 'permissions',
                    'type_options' => [
                        'abilities' => 'view_action_artifacts',
                        'arguments' => [
                            [
                                'model' => 'process',
                                'relations' => ['processType'],
                                'source' => 'actions_process_id',
                            ],
                            [
                                'model' => '',
                                'relations' => [],
                                'source' => 'actions_action_type_id',
                            ],
                            [
                                'model' => 'artifact',
                                'relations' => [],
                                'source' => 'artifacts_id',
                            ]
                        ]
                    ]
                ]
            ],
            'source_type' => 'sql',
            'source' => [
                'from' => 'documents',
                'select' => [
                    'actions.id as actions_id',
                    'actions.process_id as actions_process_id',
                    'actions.identity_id as actions_identity_id',
                    'actions.action_type_name as actions_action_type_name',
                    'actions.action_type_id as actions_action_type_id',
                    'actions.process_id as actions_process_id',
                    'artifacts.id as artifacts_id',
                    'artifacts.output as artifacts_output',
                    'documents.name as documents_name',
                    'documents.mime_type as documents_mime_type',
                    'documents.size as documents_size',
                    'documents.created_at as documents_created_at',
                    'documents.sha_256 as documents_sha_256',
                    "CONCAT(users.first_name, ' ', users.last_name) as users_full_name",
                ],
                'where' => [
                    [
                        'actions.process_id',
                        '=',
                        '[[process.meta.id]]'
                    ]
                ],
                'orderBy' => [
                    [
                        'documents_created_at',
                        'desc'
                    ]
                ],
                'join' => [
                    [
                        'artifacts',
                        'artifacts.type_id',
                        '=',
                        'documents.id'
                    ],
                    [
                        'actions',
                        'actions.id',
                        '=',
                        'artifacts.owner_id'
                    ],
                    [
                        'users',
                        'users.id',
                        '=',
                        'actions.user_id'
                    ]
                ],
                'whereIn' => []
            ],
            'columns' => [
                [
                    'type' => 'url',
                    'data' => 'documents_name',
                    'sort' => 0,
                    'type_options' => [
                        'url' => '/processes/$/artifacts/$/download',
                        'bindings' => [
                            'actions_process_id',
                            'artifacts_id'
                        ],
                        'target' => '_blank',
                    ],
                    'label' => 'Name',
                    'width' => 4,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
                [
                    'type' => 'text',
                    'data' => 'actions_action_type_name',
                    'sort' => 1,
                    'type_options' => [],
                    'label' => 'Aktion',
                    'width' => 3,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
                [
                    'type' => 'text',
                    'data' => 'users_full_name',
                    'sort' => 2,
                    'type_options' => [],
                    'label' => 'Erstellt von',
                    'width' => 3,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
                [
                    'type' => 'text',
                    'data' => 'documents_created_at',
                    'sort' => 3,
                    'type_options' => [
                        'date_format' => 'd.m.Y H:i'
                    ],
                    'label' => 'Erstelldatum',
                    'width' => 2,
                    'searchable' => false,
                    'sortable' => true,
                    'color' => ''
                ]
            ],
        ]
    ],
    'process_relations' => [
        'name' => 'Prozess-Verknüpfungen',
        'slug' => 'relations',
        'description' => 'Alle Verknüpfungen des Prozesses.',
        'template' => 'process_relations',
        'data' => [
            'limit_to_user_involvement' => false,
            'limit_to_user_involvement_roles' => [],
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
                    'owner_processes.id as owner_processes_id',
                    "CONCAT('process|', processes.id, '[', processes.name, ']') as processes_pipe_notation",
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
                    ],
                    [
                        'table' => 'processes as owner_processes',
                        'on' => [
                            [
                                'owner_processes.id',
                                '=',
                                'relations.left'
                            ],
                        ],
                        'orOn' => [
                            [
                                'owner_processes.id',
                                '=',
                                'relations.right'
                            ],
                        ],
                    ],
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
                'whereColumn' => [
                    [
                        'processes.id',
                        '!=',
                        'owner_processes.id'
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
    ],
    'process_identity_relations' => [
        'name' => 'Prozess-Identität Verknüpfungen',
        'slug' => 'process-identity-relations',
        'description' => 'Alle Verknüpfungen der Prozess-Identität des Benutzers',
        'template' => 'process_identity_relations',
        'data' => [
            'limit_to_user_involvement' => false,
            'limit_to_user_involvement_roles' => [],
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
                    "CONCAT('process|', processes.id, '[', processes.name, ']') as processes_pipe_notation",
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
                        '[[auth.identity_id]]'
                    ],
                    [
                        [
                            'relations.left',
                            '=',
                            '[[auth.identity_id]]'
                        ],
                        [
                            'relations.right',
                            '=',
                            '[[auth.identity_id]]'
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
    ],
    'custom' => [
        'name' => 'Eigenes SQL',
        'slug' => 'custom',
        'description' => '',
        'template' => 'custom',
        'roles' => [],
        'data' => [
            'limit_to_user_involvement' => false,
            'limit_to_user_involvement_roles' => [],
            'rows_per_page' => 10,
            'header_button' => [],
            'enable_label' => true,
            'enable_search' => true,
            'enable_pagination' => true,
            'enable_total_count' => true,
            'quick_filter' => [],
            'source_type' => 'sql',
            'source' => [
                'from' => '',
                'select' => [],
                'join' => [],
                'orderBy' => [],
            ],
            'columns' => [],
        ]
    ],
    'relation' => [
        'name' => '',
        'slug' => '',
        'description' => 'relation',
        'template' => null,
        'data' => [
            'header_button' => [],
            'rows_per_page' => 10,
            'enable_label' => true,
            'enable_search' => true,
            'enable_pagination' => true,
            'enable_total_count' => true,
            'quick_filter' => [],
            'source_type' => 'relation',
            'source' => [
                'context' => 'process_type',
                'relation' => 'roles',
                'select' => [
                    [
                        'data' => 'name',
                        'alias' => 'process_type_roles_name',
                    ],
                    [
                        'data' => 'description',
                        'alias' => 'process_type_roles_description',
                    ],
                    [
                        'data' => 'pipe_notation',
                        'alias' => 'process_type_roles_pipe_notation',
                    ],
                ]
            ],
            'columns' => [
                [
                    'type' => 'text',
                    'data' => 'process_type_roles_name',
                    'sort' => 0,
                    'type_options' => [],
                    'label' => 'Name',
                    'width' => 4,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
                [
                    'type' => 'text',
                    'data' => 'process_type_roles_description',
                    'sort' => 1,
                    'type_options' => [],
                    'label' => 'Beschreibung',
                    'width' => 8,
                    'searchable' => true,
                    'sortable' => true,
                    'color' => ''
                ],
            ],
        ]
    ],
    'connector_request' => [
        'name' => '',
        'slug' => '',
        'description' => '',
        'template' => 'connector_request',
        'locked' => false,
        'data' => [
            'limit_to_user_involvement' => false,
            'limit_to_user_involvement_roles' => [],
            'rows_per_page' => 10,
            'header_button' => [],
            'enable_label' => true,
            'enable_search' => true,
            'enable_pagination' => true,
            'enable_total_count' => false,
            'source_type' => 'connector_request',
            'source' => [
                'connector_identifier' => '',
                'request_identifier' => '',
                'list_root' => '',
                'query_mapping' => [
                    'search' => [
                        'name' => ''
                    ],
                    'columns' => [
                        'name' => '',
                        'separator' => ''
                    ],
                    'page' => [
                        'name' => ''
                    ],
                    'rows_per_page' => [
                        'name' => ''
                    ],
                    'sort_column' => [
                        'name' => ''
                    ],
                    'sort_direction' => [
                        'name' => '',
                        'asc' => '',
                        'desc' => ''
                    ]
                ],
                'select' => []
            ],
            'columns' => [],
        ]
    ],
];
