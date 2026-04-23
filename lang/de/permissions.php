<?php

return [
    'manage' => 'Verwalten',
    'view' => 'Einsehen',
    'update' => 'Ändern',
    'create' => 'Erstellen',
    'delete' => 'Löschen',

    'name' => [
        // Systemrechte
        'account_manage' => 'Konto verwalten',
        'app_manage' => 'System konfigurieren',

        'users_view' => 'Benutzer einsehen',
        'users_create' => 'Benutzer erstellen',
        'users_update' => 'Benutzer ändern',
        'users_delete' => 'Benutzer löschen',

        'groups_view' => 'Gruppen einsehen',
        'groups_create' => 'Gruppen erstellen',
        'groups_update' => 'Gruppen ändern',
        'groups_delete' => 'Gruppen löschen',

        'roles_view' => 'System-Rollen einsehen',
        'roles_create' => 'System-Rollen erstellen',
        'roles_update' => 'System-Rollen ändern',
        'roles_delete' => 'System-Rollen löschen',

        'process_type_metas_view' => 'Prozesstypen einsehen',
        'process_type_metas_create' => 'Prozesstypen erstellen',
        'process_type_metas_update' => 'Prozesstypen entwickeln',
        'process_type_metas_delete' => 'Prozesstypen löschen',

        'connectors_view' => 'Connectoren',
        'connectors_create' => 'Connectoren einsehen',
        'connectors_update' => 'Connectoren bearbeiten',
        'connectors_delete' => 'Connectoren löschen',

        'process_type_process_view' => 'Prozess einsehen',
        'process_type_process_view_situation' => 'Prozess-Situation einsehen',
        'process_type_process_view_data' => 'Prozess-Daten einsehen',
        'process_type_process_view_actions' => 'Prozess-Aktionen einsehen',
        'process_type_process_view_history' => 'Prozess-Verlauf einsehen',
        'process_type_process_view_artifacts' => 'Prozess-Artefakte einsehen',
        'process_type_process_execute_actions' => 'Aktionen ausführen',
        'process_type_process_delete' => 'Prozess löschen',

        // Gruppenrechte
        'group_view' => 'Gruppe einsehen',
        'group_update' => 'Gruppe ändern',

        'group_user_view' => 'Mitglieder einsehen',
        'group_user_create' => 'Mitglieder hinzufügen',
        'group_user_update' => 'Mitglieder ändern',
        'group_user_delete' => 'Mitglieder entfernen',

        'group_role_view' => 'Gruppen-Rollen einsehen',
        'group_role_create' => 'Gruppen-Rollen hinzufügen',
        'group_role_update' => 'Gruppen-Rollen ändern',
        'group_role_delete' => 'Gruppen-Rollen entfernen',

        // Prozesstyp
        'process_type_meta_process_create' => 'Prozess-Instanzen erstellen',
        'process_type_meta_process_delete' => 'Prozess-Instanzen löschen',
    ],

    'description' => [
        'users_permissions' => 'Rechte für die Benutzerverwaltung des Systems.',
        'groups_permissions' => 'Rechte für die Gruppenverwaltung des Systems.',
        'roles_permissions' => 'Rechte für die Rollenverwaltung des Systems.',
        'process_types_permissions' => 'Rechte für die Prozesstypverwaltung des Systems.',

        'group_permissions' => 'Legt fest, ob Benutzer dieser Gruppen-Rolle die Gruppe einsehen oder bearbeiten dürfen.',
        'group_members_permissions' => 'Definiert die Rechte für die Mitgliederverwaltung dieser Gruppe.',
        'group_roles_permissions' => 'Definiert die Rechte für die Rollenverwaltung dieser Gruppe.',
        'group_process_types_permissions' => 'Definiert die Rechte für die Verwaltung der Prozesstypen dieser Gruppe.',

        // Systemrechte
        'account_manage' => 'Eigenes Konto einsehen und bearbeiten.',
        'app_manage' => 'Vollständigen Zugriff auf alle Bereiche des Systems (Admin-Rechte).',

        'users_view' => 'Einsehen aller Benutzer der Anwendung inklusive Gruppenzuordnungen.',
        'users_create' => 'Erstellen von neuen Benutzern der Anwendung.',
        'users_update' => 'Ändern aller Benutzerdaten inklusive Passwort- und Rollen-Änderung.',
        'users_delete' => 'Löschen von Benutzern.',

        'groups_view' => 'Einsehen aller Gruppen der Anwendung inklusive Mitglieder und Prozesstypen.',
        'groups_create' => 'Erstellen von neuen Gruppen.',
        'groups_update' => 'Ändern aller Gruppen inklusive Mitglieder- und Rollenverwaltung.',
        'groups_delete' => 'Löschen von Gruppen.',

        'roles_view' => 'Einsehen aller Rollen der Anwendung inklusive der Rechte.',
        'roles_create' => 'Erstellen von System-Gruppen.',
        'roles_update' => 'Ändern aller System-Rollen inklusive Rechteverwaltung.',
        'roles_delete' => 'Löschen von System-Rollen.',

        'process_type_metas_view' => 'Prozesstypen und ihre Konfiguration einsehen, inklusive der Bots, Rollen und Kollektionen.',
        'process_type_metas_create' => 'Prozesstypen erstellen inklusive Entwicklung.',
        'process_type_metas_update' => 'Entwicklung aller bestehenden Prozesstypen.',
        'process_type_metas_delete' => 'Löschen von Prozesstypen inklusive Bots, Rollen und Kollektionen.',

        'connectors_view' => 'Connectoren und deren Anfragen einsehen.',
        'connectors_create' => 'Connectoren erstellen und Anfragen hinzufügen.',
        'connectors_update' => 'Connectoren und deren Anfragen bearbeiten.',
        'connectors_delete' => 'Connectoren und deren Anfragen löschen.',

        'process_type_process_view' => 'Einsehen der grundlegenden Prozess-Metadaten.',
        'process_type_process_view_situation' => 'Einsehen der aktuellen Prozess-Situation.',
        'process_type_process_view_data' => 'Einsehen der aktuellen Prozess-Daten.',
        'process_type_process_view_actions' => 'Alle Aktionen und Aktions-Komponenten einsehen.',
        'process_type_process_view_history' => 'Aktionsverlauf und Situationsveränderungen einsehen.',
        'process_type_process_view_artifacts' => 'Einsehen der erzeugten Dokumente und E-Mails.',
        'process_type_process_execute_actions' => 'Ausführen der Aktionen des Prozesses inklusive aller nativen Management-Aktionen.',
        'process_type_process_delete' => 'Löschen der Prozess-Instanz inklusive aller Daten und Artefakte.',

        // Gruppenrechte
        'group_view' => 'Gruppenübersicht mit Mitgliedern und Prozesstypen einsehen.',
        'group_update' => 'Informationen der Gruppe ändern.',

        'group_user_view' => 'Mitglieder und ihre Gruppen-Rolle einsehen.',
        'group_user_create' => 'Mitglieder zur Gruppe hinzufügen und Gruppen-Rolle erteilen.',
        'group_user_update' => 'Mitglieder der Gruppe ändern inklusive deren Gruppen-Rolle.',
        'group_user_delete' => 'Mitglieder aus der Gruppe entfernen.',

        'group_role_view' => 'Gruppen-Rollen und ihre Rechte einsehen.',
        'group_role_create' => 'Gruppen-Rollen erstellen inklusive zuweisen von Rechten.',
        'group_role_update' => 'Gruppen-Rollen ändern inklusive ihrer Rechte.',
        'group_role_delete' => 'Löschen von Gruppen-Rollen inklusive ihrer Rechte und Mitglieder-Zuordnungen.',

        // Prozesstyp
        'process_type_meta_process_create' => 'Erstellen von neuen Prozessen aller Versionen.',
        'process_type_meta_process_delete' => 'Löschen von Prozessen aller Versionen.',
    ]
];
