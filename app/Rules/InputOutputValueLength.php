<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class InputOutputValueLength
 * @package App\Rules
 */
class InputOutputValueLength implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (is_string($value)) {
            if (strlen($value) > 10000) {
                $fail('Der Wert darf nicht länger als 10.000 Zeichen sein.')->translate();
            }

            return;
        }
        if (is_array($value)) {
            if (strlen(json_encode($value)) > 10000) {
                $fail('Der Wert darf nicht länger als 10.000 Zeichen sein.')->translate();
            }
        }
    }

}
