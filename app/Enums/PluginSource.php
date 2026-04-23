<?php

namespace App\Enums;

/**
 * Plugin sources.
 */
enum PluginSource: string {

    case Internal = 'internal';

    case External = 'external';
}
