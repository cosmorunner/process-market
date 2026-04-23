<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidPlatformIdentifierFormat
 * @package App\Rules
 */
class ValidPlatformIdentifierFormat implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!preg_match('/^[a-z][a-z0-9-]+$/', $value)) {
            $fail('Ungültiger Name');
        }
    }

}
