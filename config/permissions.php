<?php
/**
 * Mögliche Berechtigungen
 */

return [
    'general' => [
        [
            'name' => 'Alle Prozess-Daten einsehen',
            'description' => 'Einsehen der Prozessdaten.',
            'ident' => 'process_type.process.view_data',
        ],
        [
            'name' => 'Alle Aktionen ausführen',
            'description' => 'Ausführen aller Aktionen des Prozesses.',
            'ident' => 'process_type.process.execute_actions',
        ],
        [
            'name' => 'Alle Aktionen einsehen',
            'description' => 'Alle Aktionen einsehen.',
            'ident' => 'process_type.process.view_actions',
        ],
        [
            'name' => 'Alle Aktions-Ausführungen einsehen',
            'description' => 'Alle Aktions-Ausführungen einsehen.',
            'ident' => 'process_type.process.view_action_executions',
        ],
        [
            'name' => 'Alle Situationsveränderungen einsehen',
            'description' => 'Alle Situationsveränderungen einsehen.',
            'ident' => 'process_type.process.view_action_status_changes',
        ],
        [
            'name' => 'Alle Aktionsdaten einsehen',
            'description' => 'Alle Aktionsdaten einsehen.',
            'ident' => 'process_type.process.view_action_data',
        ],
        [
            'name' => 'Alle Prozessor-Ausführungen einsehen',
            'description' => 'Alle Prozessor-Ausführungen einsehen.',
            'ident' => 'process_type.process.view_action_processor_executions',
        ],
        [
            'name' => 'Alle Artefakte einsehen',
            'description' => 'Alle Artefakte einsehen.',
            'ident' => 'process_type.process.view_action_artifacts',
        ],
        [
            'name' => 'Alle Dokumente einsehen',
            'description' => 'Alle Dokumente einsehen.',
            'ident' => 'process_type.process.view_action_artifacts_documents',
        ],
        [
            'name' => 'Alle E-Mails einsehen',
            'description' => 'Alle E-Mails einsehen.',
            'ident' => 'process_type.process.view_action_artifacts_emails',
        ],
        [
            'name' => 'Alle Aktionen exportieren',
            'description' => 'Alle Aktionen exportieren.',
            'ident' => 'process_type.process.export_actions',
        ],
        [
            'name' => 'Alle Status einsehen',
            'description' => 'Einsehen der aktuellen Prozess-Situation.',
            'ident' => 'process_type.process.view_situation',
        ],
        [
            'name' => 'Alle Listen einsehen',
            'description' => 'Prozess-Listen mit deren Inhalten einsehen.',
            'ident' => 'process_type.process.view_lists',
        ],
        [
            'name' => 'Aktionen rückgängig machen',
            'description' => 'Die Ausführung einer Aktion rückgängig machen. Der Prozess wird auf die Situation vor der Aktionsausführung gesetzt.',
            'ident' => 'process_type.process.revert_actions',
        ],
        [
            'name' => 'Aktions-Verlauf einsehen',
            'description' => 'Aktionsverlauf und Situationsveränderungen einsehen.',
            'ident' => 'process_type.process.view_history',
        ],
        [
            'name' => 'Alle Menü-Einträge einsehen',
            'description' => 'Alle Menü-Menüpunkte sehen.',
            'ident' => 'process_type.process.view_menu_items',
        ],
        [
            'name' => 'Alle Artefakte sehen',
            'description' => 'Einsehen der erzeugten Dokumente und E-Mails.',
            'ident' => 'process_type.process.view_artifacts',
        ]
    ],
    'action_type' => [
        [
            'name' => 'Aktion ausführen',
            'description' => 'Prozess-Aktion öffnen und ausführen.',
            'ident' => 'process_type.action_type.*.execute',
        ],
        [
            'name' => 'Einsehen',
            'description' => 'Einsehen.',
            'ident' => 'process_type.action_type.*.view',
        ],
        [
            'name' => 'Exportieren',
            'description' => 'Exportieren.',
            'ident' => 'process_type.action_type.*.export',
        ],
        [
            'name' => 'Ausführungen einsehen',
            'description' => 'Ausführungen einsehen.',
            'ident' => 'process_type.action_type.*.view_execution',
        ],
        [
            'name' => 'Situationsveränderungen einsehen',
            'description' => 'Situationsveränderungen einsehen.',
            'ident' => 'process_type.action_type.*.view_status_changes',
        ],
        [
            'name' => 'Aktionsdaten einsehen',
            'description' => 'Aktionsdaten einsehen.',
            'ident' => 'process_type.action_type.*.view_data',
        ],
        [
            'name' => 'Prozessorausführungen einsehen',
            'description' => 'Prozessorausführungen einsehen.',
            'ident' => 'process_type.action_type.*.view_processor_executions',
        ],
        [
            'name' => 'Alle Artefakte einsehen',
            'description' => 'Alle Artefakte einsehen.',
            'ident' => 'process_type.action_type.*.view_artifacts',
        ],
        [
            'name' => 'Alle Dokumente einsehen',
            'description' => 'Alle Dokumente einsehen.',
            'ident' => 'process_type.action_type.*.view_artifacts_documents',
        ],
        [
            'name' => 'Alle E-Mails einsehen',
            'description' => 'Alle E-Mails einsehen.',
            'ident' => 'process_type.action_type.*.view_artifacts_emails',
        ]
    ],
    'status_type' => [
        [
            'name' => 'Status einsehen',
            'description' => 'Status einsehen',
            'ident' => 'process_type.status_type.*.view',
        ]
    ],
    'list_config' => [
        [
            'name' => 'Liste einsehen',
            'description' => 'Prozess-Liste öffnen und und Inhalte sehen.',
            'ident' => 'process_type.list_config.*.view',
        ]
    ],
    'menu_item' => [
        [
            'name' => 'Navigations-Eintrag sehen',
            'description' => 'Navigations-Eintrag sehen.',
            'ident' => 'process_type.menu_item.*.view',
        ]
    ],
    'output' => [
        [
            'name' => 'Prozess-Datensatz sehen',
            'description' => 'Prozess-Datensatz sehen.',
            'ident' => 'process_type.output.*.view',
        ]
    ],
];
