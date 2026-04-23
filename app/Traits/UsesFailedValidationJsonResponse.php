<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Überschreibt die native failedValidation Response um die Message zu übersetzen.
 * Trait UsesFailedValidationResponse
 * @package App\Traits
 */
trait UsesFailedValidationJsonResponse {

    /**
     * Übersetzte Fehlermeldung bei fehlgeschlagener Eingabevalidierung.
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Ungültige Daten.',
            'errors' => $validator->errors()
        ], 422));
    }
}
