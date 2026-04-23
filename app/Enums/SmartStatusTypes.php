<?php

namespace App\Enums;

/**
 * Aufgaben-Typen.
 */
enum SmartStatusTypes: string {

    /**
     * A smart status type that gets its value from the number of relations.
     */
    case RelationType = 'relation_type';

    /**
     * A smart status type that gets its value from the a custom logic template rendering.
     */
    case CustomLogic = 'custom_logic';

    /**
     * A smart status type that gets its value from the a status of a related process.
     */
    case RelatedStatus = 'related_status';
}
