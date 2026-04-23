<?php

namespace App;

use Illuminate\Support\Facades\Route;

/**
 * Je nach "ref" Parameter gibt diese Klasse eine URL zurück zu der weitergeleitet werden soll.
 * Class ReferrerRedirector
 * @package App
 */
class ReferrerRedirector {

    /**
     * Ermittelt basierend auf $ref die URL zu der weitergeleitet werden soll.
     * @param string|null $ref
     * @param string|null $fallbackUrl
     * @return string|null
     */
    public static function to(string $ref = null, string $fallbackUrl = null) {
        // Falls $ref eine URL ist.
        if (filter_var($ref, FILTER_VALIDATE_URL)) {
            $fallbackUrl = $ref;
            $ref = null;
        }

        if ($ref === null) {
            return $fallbackUrl;
        }

        if (Route::has($ref)) {
            return route($ref);
        }

        return $fallbackUrl;
    }
}
