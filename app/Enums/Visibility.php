<?php

namespace App\Enums;

/**
 * Sichtbarkeit von Entitits
 */
enum Visibility: int {

    // Nur Author kann die Entität sehen.
    case Private = 0;

    // Link zur Entität wird benötigt. Andernfalls ist die Entität nicht sichtbar.
    case Hidden = 1;

    // Entität ist öffentlich sichtbar.
    case Public = 2;
}
