<?php

return [
    'manage' => 'Verwalten',
    'view' => 'View',
    'update' => 'Update',
    'create' => 'Create',
    'delete' => 'Delete',

    'name' => [
        // Systemrechte
        'account_manage' => 'Manage account',
        'app_manage' => 'Manage system',

        'users_view' => 'View users',
        'users_create' => 'Create users',
        'users_update' => 'Edit users',
        'users_delete' => 'Delete users',

        'groups_view' => 'View groups',
        'groups_create' => 'Create groups',
        'groups_update' => 'Edit groups',
        'groups_delete' => 'Delete groups',

        'roles_view' => 'View system roles',
        'roles_create' => 'Create system roles',
        'roles_update' => 'Edit system roles',
        'roles_delete' => 'Delete system roles',

        'process_type_metas_view' => 'View process types',
        'process_type_metas_create' => 'Create process types',
        'process_type_metas_update' => 'Update process types',
        'process_type_metas_delete' => 'Delete process types',

        'connectors_view' => 'View connectors',
        'connectors_create' => 'Create connectors',
        'connectors_update' => 'Update connectors',
        'connectors_delete' => 'Delete connectors',

        'process_type_process_view' => 'View process',
        'process_type_process_view_situation' => 'View process situation',
        'process_type_process_view_actions' => 'View process actions',
        'process_type_process_view_history' => 'View process history',
        'process_type_process_view_artifacts' => 'View process artifacts',
        'process_type_process_execute_actions' => 'Execute actions',
        'process_type_process_update' => 'Work on process',
        'process_type_process_delete' => 'Delete process',

        // Gruppenrechte
        'group_view' => 'View group',
        'group_update' => 'Edit group',

        'group_user_view' => 'View group members',
        'group_user_create' => 'Add group members',
        'group_user_update' => 'Edit group members',
        'group_user_delete' => 'Remove group members',

        'group_role_view' => 'View group roles',
        'group_role_create' => 'Create group roles',
        'group_role_update' => 'Edit group roles',
        'group_role_delete' => 'Delete group roles',

        // Prozesstyp
        'process_type_process_create' => 'Create processes',
        'process_type_process_manage' => 'Manage processes',
    ],

    'description' => [
        'users_permissions' => 'Defines the rights for the user administration of the system.',
        'groups_permissions' => 'Defines the rights for the group administration of the system.',
        'roles_permissions' => 'Defines the rights for the role administration of the system.',

        'group_permissions' => 'Determines whether users of this group role can view or edit the group.',
        'group_members_permissions' => 'Defines the permissions for the member administration of this group.',
        'group_roles_permissions' => 'Defines the rights for the role administration of this group.',
        'group_process_types_permissions' => 'Defines the rights for the process type management of this group.',

        // System rights
        'account_manage' => 'View and edit your own account.',
        'app_manage' => 'Full access to all areas of the system (admin rights).',

        'users_view' => 'View all users of the application including group memberships.',
        'users_create' => 'Create new users of the application.',
        'users_update' => 'Change all user data including password and role changes.',
        'users_delete' => 'Remove users from the application.',

        'groups_view' => 'View all groups of the application including members and process types.',
        'groups_create' => 'Create new groups.',
        'groups_update' => 'Change all groups including members and roles.',
        'groups_delete' => 'Remove groups from the application.',

        'roles_view' => 'View all roles of the application including their permissions.',
        'roles_create' => 'Create a system group.',
        'roles_update' => 'Change all system roles including their permissions.',
        'roles_delete' => 'Delete system roles.',

        'process_type_metas_view' => 'View process types and their configuration, including bots, roles and collections.',
        'process_type_metas_create' => 'Create process types and develop them.',
        'process_type_metas_update' => 'Develop all existing process types.',
        'process_type_metas_delete' => 'Delete process types including their bots, roles and collections.',

        'connectors_view' => 'View connectors and their requests.',
        'connectors_create' => 'Create connectors and add requests to them.',
        'connectors_update' => 'Develop and update connectors and their requests.',
        'connectors_delete' => 'Delete connectors and the requests.',

        'process_type_process_view' => 'View process metadata, status, actions and history.',
        'process_type_process_view_situation' => 'View current process situation.',
        'process_type_process_view_actions' => 'View all actions and action components..',
        'process_type_process_view_history' => 'View executed actions and situation history',
        'process_type_process_view_artifacts' => 'View generated documents and sent emails.',
        'process_type_process_execute_actions' => 'Execute actions including all native management actions.',
        'process_type_process_update' => 'Execute actions of the process including all native management actions.',
        'process_type_process_delete' => 'Löschen der Prozess-Instanz inklusive aller Daten und Artefakte.',

        // Group rights

        'group_view' => 'View group overview with members and process types.',
        'group_update' => 'Change group information.',

        'group_user_view' => 'View members and their group role.',
        'group_user_create' => 'Add members to the group and assign a group role.',
        'group_user_update' => 'Change members of group including their group role.',
        'group_user_delete' => 'Remove members from the group.',

        'group_role_view' => 'View group roles and their permissions.',
        'group_role_create' => 'Create group roles and assign permissions.',
        'group_role_update' => 'Change all group roles including their permissions.',
        'group_role_delete' => 'Delete group roles including their permissions and member assignments.',

        // Prozesstyp
        'process_type_meta_process_create' => 'Create new process instances.',
        'process_type_meta_process_manage' => 'Administrative process management. Allows groups and users to grant or withdraw access to the process. Right to delete the process.',
    ]
];
