<?php

namespace App\Enums;

/**
 * Granted to process type roles.
 */
enum ProcessRolePermissions: string {

    // Prefix is process_type.process.
    case ViewSituation = 'process_type.process.view_situation';
    case ViewData = 'process_type.process.view_data';
    case ViewHistory = 'process_type.process.view_history';
    case ViewArtifacts = 'process_type.process.view_artifacts';
    case ViewLists = 'process_type.process.view_lists';
    case ViewMenuItems = 'process_type.process.view_menu_items';
    case ExecuteActions = 'process_type.process.execute_actions';
    case ViewActions = 'process_type.process.view_actions';
    case ViewActionExecutions = 'process_type.process.view_action_executions';
    case ViewActionStatusChanges = 'process_type.process.view_action_status_changes';
    case ViewActionData = 'process_type.process.view_action_data';
    case ViewActionProcessorExecutions = 'process_type.process.view_action_processor_executions';
    case ViewActionArtifacts = 'process_type.process.view_action_artifacts';
    case ViewActionArtifactsDocuments = 'process_type.process.view_action_artifacts_documents';
    case ViewActionArtifactsEmails = 'process_type.process.view_action_artifacts_emails';
    case ExportActions = 'process_type.process.export_actions';
    case RevertActions = 'process_type.process.revert_actions';
    case Restore = 'process_type.process.restore';
    case Forcedelete = 'process_type.process.forcedelete';

    // Prefix is process_type.action_type.%s.
    case ExecuteActiontype = 'process_type.action_type.%s.execute';
    case ViewActiontype = 'process_type.action_type.%s.view';
    case ViewActiontypeExecution = 'process_type.action_type.%s.view_execution';
    case ViewActiontypeStatusChanges = 'process_type.action_type.%s.view_status_changes';
    case ViewActiontypeData = 'process_type.action_type.%s.view_data';
    case ViewActiontypeProcessorExecutions = 'process_type.action_type.%s.view_processor_executions';
    case ViewActiontypeArtifacts = 'process_type.action_type.%s.view_artifacts';
    case ViewActiontypeArtifactsDocuments = 'process_type.action_type.%s.view_artifacts_documents';
    case ViewActiontypeArtifactsEmails = 'process_type.action_type.%s.view_artifacts_emails';
    case ExportActiontype = 'process_type.action_type.%s.export';

    // Prefix is process_type.
    case ViewStatustype = 'process_type.status_type.%s.view';
    case ViewListConfig = 'process_type.list_config.%s.view';
    case ViewOutput = 'process_type.output.%s.view';
    case ViewMenuItem = 'process_type.menu_item.%s.view';
}
