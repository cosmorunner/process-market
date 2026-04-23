<?php

namespace App\Enums;

/**
 * In der Datenbank gespeicherte Einstellungen
 */
enum Settings: string {

    case AllisaToken = 'allisa_token';
    case AllisaTokenExpiresAt = 'allisa_token_expires_at';
    case AllisaConsoleToken = 'allisa_console_token';
    case AllisaConsoleTokenExpiresAt = 'allisa_console_token_expires_at';

    case TemplatePreviewDatasets = 'template-preview-datasets';

}
