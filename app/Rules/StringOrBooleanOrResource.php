<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Prüft ob der Wert ein String oder ein Boolean ist.
 * Class StringOrBooleanOrResource
 * @package App\Rules
 */
class StringOrBooleanOrResource implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!(is_string($value) || is_bool($value) || is_resource($value))) {
            $fail('Nur String-Wert oder TRUE/FALSE erlaubt.');
        }
    }

}
