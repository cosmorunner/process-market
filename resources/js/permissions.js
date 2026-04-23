export default {
    general: [
        {
            name: 'Alle Prozess-Daten einsehen',
            ident: 'process_type.process.view_data'
        },
        {
            name: 'Alle Aktionen ausführen',
            ident: 'process_type.process.execute_actions'
        },
        {
            name: 'Alle Aktionen einsehen',
            ident: 'process_type.process.view_actions'
        },
        {
            name: 'Alle Aktions-Ausführungen einsehen (Alle Details)',
            ident: 'process_type.process.view_action_executions',
            sub_permissions: [
                {
                    name: 'Alle Situationsveränderungen einsehen',
                    ident: 'process_type.process.view_action_status_changes'
                },
                {
                    name: 'Alle Aktions-Daten einsehen',
                    ident: 'process_type.process.view_action_data'
                },
                {
                    name: 'Alle Prozessor-Ausführungen einsehen',
                    ident: 'process_type.process.view_action_processor_executions'
                },
                {
                    name: 'Alle Aktions-Artefakte einsehen',
                    ident: 'process_type.process.view_action_artifacts',
                    sub_permissions: [
                        {
                            name: 'Alle Dokumente einsehen',
                            ident: 'process_type.process.view_action_artifacts_documents'
                        },
                        {
                            name: 'Alle E-Mails einsehen',
                            ident: 'process_type.process.view_action_artifacts_emails'
                        }
                    ]
                }
            ]
        },
        {
            name: 'Alle Aktionen exportieren',
            ident: 'process_type.process.export_actions'
        },
        {
            name: 'Alle Status einsehen',
            ident: 'process_type.process.view_situation'
        },
        {
            name: 'Alle Listen einsehen',
            ident: 'process_type.process.view_lists'
        },
        {
            name: 'Alle Artefakte einsehen',
            ident: 'process_type.process.view_artifacts'
        },
        {
            name: 'Alle Menü-Einträge einsehen',
            ident: 'process_type.process.view_menu_items'
        },
        {
            name: 'Aktionen rückgängig machen',
            ident: 'process_type.process.revert_actions'
        },
        {
            name: 'Aktions-Verlauf einsehen',
            ident: 'process_type.process.view_history'
        }
    ],
    action_type: [
        {
            name: 'Ausführen',
            ident: 'process_type.action_type.*.execute'
        },
        {
            name: 'Einsehen',
            ident: 'process_type.action_type.*.view'
        },
        {
            name: 'Exportieren',
            ident: 'process_type.action_type.*.export'
        },
        {
            name: 'Ausführungen einsehen (Alle Details)',
            ident: 'process_type.action_type.*.view_execution',
            sub_permissions: [
                {
                    name: 'Situationsveränderungen einsehen',
                    ident: 'process_type.action_type.*.view_status_changes'
                },
                {
                    name: 'Aktions-Daten einsehen',
                    ident: 'process_type.action_type.*.view_data'
                },
                {
                    name: 'Prozessorausführungen einsehen',
                    ident: 'process_type.action_type.*.view_processor_executions'
                },
                {
                    name: 'Alle Aktions-Artefakte einsehen',
                    ident: 'process_type.action_type.*.view_artifacts',
                    sub_permissions: [
                        {
                            name: 'Alle Dokumente einsehen',
                            ident: 'process_type.action_type.*.view_artifacts_documents'
                        },
                        {
                            name: 'Alle E-Mails einsehen',
                            ident: 'process_type.action_type.*.view_artifacts_emails'
                        }
                    ]
                }
            ]
        },
    ]
};
