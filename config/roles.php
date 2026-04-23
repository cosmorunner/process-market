<?php
/**
 * Avaiable permissions
 */

return [
    'owner' => [
        'permissions' => [
            'demos.view',
            'processes.view',
            'solutions.view',
            'licenses.view',
            'processes.create',
            'processes.update',
            'process_versions.create',
            'process_versions.export',
            'solutions.create',
            'solutions.update',
            'solution_versions.create',
            'solution_versions.export',
            'members.view',
            'processes.delete',
            'solutions.delete',
            'licenses.manage',
            'platforms.manage',
            'members.manage',
            'organisation.manage',
            'admins.manage'
        ]
    ],
    'admin' => [
        'permissions' => [
            'demos.view',
            'processes.view',
            'solutions.view',
            'licenses.view',
            'processes.create',
            'processes.update',
            'process_versions.create',
            'process_versions.export',
            'solutions.create',
            'solutions.update',
            'solution_versions.create',
            'solution_versions.export',
            'members.view',
            'processes.delete',
            'solutions.delete',
            'licenses.manage',
            'platforms.manage',
            'members.manage',
            'organisation.manage',
        ]
    ],
    'manager' => [
        'permissions' => [
            'demos.view',
            'processes.view',
            'solutions.view',
            'licenses.view',
            'processes.create',
            'processes.update',
            'process_versions.create',
            'process_versions.export',
            'solutions.create',
            'solutions.update',
            'solution_versions.create',
            'solution_versions.export',
            'members.view',
            'processes.delete',
            'solutions.delete',
            'licenses.manage',
            'platforms.manage',
        ]
    ],
    'developer' => [
        'permissions' => [
            'demos.view',
            'processes.view',
            'solutions.view',
            'licenses.view',
            'processes.create',
            'processes.update',
            'process_versions.create',
            'process_versions.export',
            'solutions.create',
            'solutions.update',
            'solution_versions.create',
            'solution_versions.export',
            'members.view',
        ]
    ],
    'reporter' => [
        'permissions' => [
            'demos.view',
            'processes.view',
            'solutions.view',
            'licenses.view',
        ]
    ],
];
