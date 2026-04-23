<?php

return [
    'tables' => [
        [
            'label' => 'Zugriffe',
            'name' => 'accesses',
        ],
        [
            'label' => 'Aktionen',
            'name' => 'actions',
        ],
        [
            'label' => 'Artefakte',
            'name' => 'artifacts',
        ],
        [
            'label' => 'Dokumente',
            'name' => 'documents',
        ],
        [
            'label' => 'E-Mails',
            'name' => 'emails',
        ],
        [
            'label' => 'Gruppen',
            'name' => 'groups',
        ],
        [
            'label' => 'Prozesstyp-Meta',
            'name' => 'process_type_metas',
        ],
        [
            'label' => 'Prozesse',
            'name' => 'processes',
        ],
        [
            'label' => 'Verknüpfungen',
            'name' => 'relations',
        ],
        [
            'label' => 'Rollen',
            'name' => 'roles',
        ],
        [
            'label' => 'Benutzer',
            'name' => 'users',
        ]
    ],
    'columns' => [
        'accesses' => [
            [
                'column' => 'accesses.id',
                'alias' => 'accesses_id',
                'label' => 'Zugriff - id'
            ],
            [
                'column' => 'accesses.role_id',
                'alias' => 'accesses_role_id',
                'label' => 'Zugriff - Rollen-Id'
            ],
            [
                'column' => 'accesses.resource_id',
                'alias' => 'accesses_resource_id',
                'label' => 'Zugriff - Resourcen-Id'
            ],
            [
                'column' => 'accesses.recipient_id',
                'alias' => 'accesses_recipient_id',
                'label' => 'Zugriff - Berechtigter-Id'
            ],
            [
                'column' => 'accesses.recipient_type',
                'alias' => 'accesses_recipient_type',
                'label' => 'Zugriff - Berechtigter-Typ'
            ],
            [
                'column' => 'accesses.created_at',
                'alias' => 'accesses_created_at',
                'label' => 'Zugriff - Erstelldatum'
            ]
        ],
        'actions' => [
            [
                'column' => 'actions.id',
                'alias' => 'actions_id',
                'label' => 'Aktion - Id'
            ],
            [
                'column' => 'actions.process_id',
                'alias' => 'actions_process_id',
                'label' => 'Aktion - Prozess-Id'
            ],
            [
                'column' => 'actions.identity_id',
                'alias' => 'actions_identity_id',
                'label' => 'Aktion - Benutzer - Prozess-Identität Id'
            ],
            [
                'column' => 'actions.action_type_id',
                'alias' => 'actions_action_type_id',
                'label' => 'Aktion - Aktionstyp-Id'
            ],
            [
                'column' => 'actions.action_type_name',
                'alias' => 'actions_action_type_name',
                'label' => 'Aktion - Aktionstyp-Name'
            ],
            [
                'column' => 'actions.created_at',
                'alias' => 'actions_created_at',
                'label' => 'Aktion - Erstelldatum'
            ],
            [
                'column' => "CONCAT('action|', actions.id, '[', actions.action_type_name, ']')",
                'alias' => 'actions_pipe_notation',
                'label' => 'Aktion - Pipe-Notation'
            ],
        ],
        'artifacts' => [
            [
                'column' => 'artifacts.id',
                'alias' => 'artifacts_id',
                'label' => 'Artefakt - Id'
            ],
            [
                'column' => 'artifacts.output',
                'alias' => 'artifacts_output',
                'label' => 'Artefakt - Aktions-Datenfeld'
            ],
        ],
        'documents' => [
            [
                'column' => "CONCAT('document|', documents.id)",
                'alias' => 'documents_pipe_notation',
                'label' => 'Dokument - Pipe-Notation'
            ],
            [
                'column' => 'documents.id',
                'alias' => 'documents_id',
                'label' => 'Dokument - Id'
            ],
            [
                'column' => 'documents.name',
                'alias' => 'documents_name',
                'label' => 'Dokument - Name'
            ],
            [
                'column' => 'documents.mime_type',
                'alias' => 'documents_mime_type',
                'label' => 'Dokument - MIME-Type'
            ],
            [
                'column' => 'documents.file_name',
                'alias' => 'documents_file_name',
                'label' => 'Dokument - Dateiname'
            ],
            [
                'column' => 'documents.size',
                'alias' => 'documents_size',
                'label' => 'Dokument - Größe'
            ],
            [
                'column' => 'documents.created_at',
                'alias' => 'documents_created_at',
                'label' => 'Dokument - Erstelldatum'
            ],
            [
                'column' => 'documents.sha_256',
                'alias' => 'documents_sha_256',
                'label' => 'Dokument - Sha_256'
            ]
        ],
        'emails' => [
            [
                'column' => "CONCAT('email|', emails.id)",
                'alias' => 'emails_pipe_notation',
                'label' => 'E-Mail - Pipe-Notation'
            ],
            [
                'column' => 'emails.id',
                'alias' => 'emails_id',
                'label' => 'E-Mail - Id'
            ],
            [
                'column' => 'emails.from',
                'alias' => 'emails_from',
                'label' => 'E-Mail - Absender'
            ],
            [
                'column' => 'emails.to',
                'alias' => 'emails_to',
                'label' => 'E-Mail - Empfänger'
            ],
            [
                'column' => 'emails.cc',
                'alias' => 'emails_cc',
                'label' => 'E-Mail - CC-Empfänger'
            ],
            [
                'column' => 'emails.bcc',
                'alias' => 'emails_bcc',
                'label' => 'E-Mail - BCC-Empfänger'
            ],
            [
                'column' => 'emails.subject',
                'alias' => 'emails_subject',
                'label' => 'E-Mail - Betreff'
            ],
            [
                'column' => 'emails.document_id',
                'alias' => 'emails_document_id',
                'label' => 'E-Mail - Dokument-Id'
            ],
            [
                'column' => 'emails.created_at',
                'alias' => 'emails_created_at',
                'label' => 'E-Mail - Erstelldatum'
            ]
        ],
        'groups' => [
            [
                'column' => "CONCAT('group|', groups.id, '[', groups.name, ']')",
                'alias' => 'groups_pipe_notation',
                'label' => 'Gruppe - Pipe-Notation'
            ],
            [
                'column' => 'groups.id',
                'alias' => 'groups_id',
                'label' => 'Gruppe - Id'
            ],
            [
                'column' => 'groups.name',
                'alias' => 'groups_name',
                'label' => 'Gruppe - Name'
            ],
            [
                'column' => 'groups.description',
                'alias' => 'groups_description',
                'label' => 'Gruppe - Beschreibung'
            ],
            [
                'column' => 'groups.aliases',
                'alias' => 'groups_aliases',
                'label' => 'Gruppe - Aliases (JSON-Array)'
            ],
            [
                'column' => 'groups.provider',
                'alias' => 'groups_provider',
                'label' => 'Gruppe - Provider'
            ],
            [
                'column' => 'groups.provider_group_id',
                'alias' => 'groups_provider_group_id',
                'label' => 'Gruppe - Provider Gruppen-Id'
            ],
            [
                'column' => 'groups.tags',
                'alias' => 'groups_tags',
                'label' => 'Gruppe - Tags (JSON-Array)'
            ],
            [
                'column' => 'groups.created_at',
                'alias' => 'groups_created_at',
                'label' => 'Gruppe - Erstelldatum'
            ],
            [
                'column' => 'groups.updated_at',
                'alias' => 'groups_updated_at',
                'label' => 'Gruppe - Aktualisierungsdatum'
            ]
        ],
        'processes' => [
            [
                'column' => "CONCAT('process|', processes.id, '[', processes.name, ']')",
                'alias' => 'processes_pipe_notation',
                'label' => 'Prozess - Pipe-Notation'
            ],
            [
                'column' => 'processes.id',
                'alias' => 'processes_id',
                'label' => 'Prozess - Id'
            ],
            [
                'column' => 'processes.name',
                'alias' => 'processes_name',
                'label' => 'Prozess - Name'
            ],
            [
                'column' => 'processes.image',
                'alias' => 'processes_image',
                'label' => 'Prozess - Icon'
            ],
            [
                'column' => 'processes.description',
                'alias' => 'processes_description',
                'label' => 'Prozess - Beschreibung'
            ],
            [
                'column' => 'processes.tags',
                'alias' => 'processes_tags',
                'label' => 'Prozess - Tags (JSON-Array)'
            ],
            [
                'column' => 'processes.reference',
                'alias' => 'processes_reference',
                'label' => 'Prozess - Referenz'
            ],
            [
                'column' => 'processes.created_at',
                'alias' => 'processes_created_at',
                'label' => 'Prozess - Erstelldatum'
            ],
            [
                'column' => 'processes.updated_at',
                'alias' => 'processes_updated_at',
                'label' => 'Prozess - Änderungsdatum'
            ],
            [
                'column' => 'owner_processes.id',
                'alias' => 'owner_processes_id',
                'label' => 'Eigentümer-Prozess - Id'
            ]
        ],
        'process_type_metas' => [
            [
                'column' => 'process_type_metas.id',
                'alias' => 'process_type_metas_id',
                'label' => 'Prozesstyp-Meta - Id'
            ],
            [
                'column' => 'process_type_metas.name',
                'alias' => 'process_type_metas_name',
                'label' => 'Prozesstyp-Meta - Name'
            ],
            [
                'column' => 'process_type_metas.description',
                'alias' => 'process_type_metas_description',
                'label' => 'Prozesstyp-Meta - Beschreibung'
            ],
            [
                'column' => 'process_type_metas.image',
                'alias' => 'process_type_metas_image',
                'label' => 'Prozesstyp-Meta - Icon'
            ],
            [
                'column' => 'process_type_metas.namespace',
                'alias' => 'process_type_metas_namespace',
                'label' => 'Prozesstyp-Meta - Namespace'
            ],
            [
                'column' => 'process_type_metas.identifier',
                'alias' => 'process_type_metas_identifier',
                'label' => 'Prozesstyp-Meta - Identifier'
            ],
            [
                'column' => 'process_type_metas.full_namespace',
                'alias' => 'process_type_metas_full_namespace',
                'label' => 'Prozesstyp-Meta - Namespace/Identifier'
            ],
        ],
        'process_types' => [
            [
                'column' => 'process_types.id',
                'alias' => 'process_types_id',
                'label' => 'Prozesstyp - Id'
            ],
            [
                'column' => 'process_types.version',
                'alias' => 'process_types_version',
                'label' => 'Prozesstyp - Version'
            ]
        ],
        'relations' => [
            [
                'column' => 'relations.relation_type_name',
                'alias' => 'relations_relation_type_name',
                'label' => 'Verknüpfung - Verknüpfungstyp-Name'
            ],
            [
                'column' => 'relations.created_at',
                'alias' => 'relations_created_at',
                'label' => 'Verknüpfung - Erstelldatum'
            ],
            [
                'column' => 'relations.single',
                'alias' => 'relations_single',
                'label' => 'Verknüpfung - Single'
            ],
            [
                'column' => 'relations.reference',
                'alias' => 'relations_reference',
                'label' => 'Verknüpfung - Referenz'
            ]
        ],
        'roles' => [
            [
                'column' => "CONCAT('role|', roles.id)",
                'alias' => 'roles_pipe_notation',
                'label' => 'Rolle - Pipe-Notation'
            ],
            [
                'column' => 'roles.id',
                'alias' => 'roles_id',
                'label' => 'Rolle - Id'
            ],
            [
                'column' => 'roles.name',
                'alias' => 'roles_name',
                'label' => 'Rolle - Name'
            ],
            [
                'column' => 'roles.description',
                'alias' => 'roles_description',
                'label' => 'Rolle - Beschreibung'
            ]
        ],
        'users' => [
            [
                'column' => "CONCAT('user|', users.id, '[', users.first_name,' ' , users.last_name, ']')",
                'alias' => 'users_pipe_notation',
                'label' => 'Benutzer - Pipe-Notation'
            ],
            [
                'column' => "CONCAT('user|', users.identity_id, '[Prozess-Identität - ', users.first_name,' ' , users.last_name, ']')",
                'alias' => 'users_identity_pipe_notation',
                'label' => 'Benutzer - Prozess-Identität - Pipe-Notation'
            ],
            [
                'column' => 'users.identity_id',
                'alias' => 'users_identity_id',
                'label' => 'Benutzer - Prozess-Identität - Id'
            ],
            [
                'column' => 'users.id',
                'alias' => 'users_id',
                'label' => 'Benutzer - Id'
            ],
            [
                'column' => 'users.first_name',
                'alias' => 'users_first_name',
                'label' => 'Benutzer - Vorname'
            ],
            [
                'column' => 'users.last_name',
                'alias' => 'users_last_name',
                'label' => 'Benutzer - Nachname'
            ],
            [
                'column' => 'users.email',
                'alias' => 'users_email',
                'label' => 'Benutzer - E-Mail'
            ],
            [
                'column' => "CONCAT(users.first_name, ' ', users.last_name)",
                'alias' => 'users_full_name',
                'label' => 'Benutzer - Ganzer Name'
            ],
            [
                'column' => 'users.aliases',
                'alias' => 'users_aliases',
                'label' => 'Benutzer - Aliases (JSON-Array)'
            ],
            [
                'column' => 'users.provider',
                'alias' => 'users_provider',
                'label' => 'Benutzer - Provider'
            ],
            [
                'column' => 'users.provider_user_id',
                'alias' => 'users_provider_user_id',
                'label' => 'Benutzer - Provider Benutzer-Id'
            ],
            [
                'column' => 'users.tags',
                'alias' => 'users_tags',
                'label' => 'Benutzer - Tags (JSON-Array)'
            ],
            [
                'column' => 'users.created_at',
                'alias' => 'users_created_at',
                'label' => 'Benutzer - Erstelldatum'
            ],
            [
                'column' => 'users.updated_at',
                'alias' => 'users_updated_at',
                'label' => 'Benutzer - Aktualisierungsdatum'
            ]
        ]
    ],
    'relations' => [
        'process_type' => [
            'roles' => [
                [
                    'column' => 'name',
                    'alias' => 'process_type_roles_name',
                    'label' => 'Rolle - Name'
                ],
                [
                    'column' => 'description',
                    'alias' => 'process_type_roles_description',
                    'label' => 'Rolle - Beschreibung'
                ],
                [
                    'column' => 'pipe_notation',
                    'alias' => 'process_type_roles_pipe_notation',
                    'label' => 'Rolle - Model-Pipe-Notation'
                ]
            ]
        ]
    ],
    'template_columns' => [
        'group_members' => [
            [
                'column' => 'id',
                'alias' => 'users_id',
                'label' => 'Benutzer - ID'
            ],
            [
                'column' => 'first_name',
                'alias' => 'users_first_name',
                'label' => 'Benutzer - Vorname'
            ],
            [
                'column' => 'last_name',
                'alias' => 'users_last_name',
                'label' => 'Benutzer - Nachname'
            ],
            [
                'column' => 'full_name',
                'alias' => 'users_full_name',
                'label' => 'Benutzer - Voller Name'
            ],
            [
                'column' => 'email',
                'alias' => 'users_email',
                'label' => 'Benutzer - E-Mail'
            ],
            [
                'column' => 'user_pipe_notation',
                'alias' => 'users_pipe_notation',
                'label' => 'Benutzer - Model-Pipe-Notation'
            ],
            [
                'column' => 'entry_date',
                'alias' => 'users_group_entry_date',
                'label' => 'Benutzer - Beitrittsdatum'
            ],
            [
                'column' => 'group_name',
                'alias' => 'groups_name',
                'label' => 'Gruppe - Name'
            ],
            [
                'column' => 'group_pipe_notation',
                'alias' => 'groups_pipe_notation',
                'label' => 'Gruppe - Model-Pipe-Notation'
            ],
            [
                'column' => 'role_name',
                'alias' => 'groups_role_name',
                'label' => 'Gruppen-Rolle - Name'
            ],
            [
                'column' => 'role_pipe_notation',
                'alias' => 'groups_role_pipe_notation',
                'label' => 'Gruppen-Rolle - Model-Pipe-Notation'
            ]
        ]
    ]
];
