<?php

namespace App\Enums;

/**
 * Plugin types.
 */
enum PluginType: string {

    case ActionTypeComponent = 'action_type_component';

    case StatusType = 'status_type';
    case CustomProcessor = 'custom_processor';
}
