<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidVersionFormat
 * @package App\Rules
 */
class ValidVersionFormat implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!preg_match("/^(\d+\.)+(\d+\.)+(\*|\d+)$/", $value)) {
            $fail('Ungültiges Versionsformat.');
        }
    }

}
