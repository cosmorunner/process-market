<?php

namespace App\Rules\Environment;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidAliasFormat
 * @package App\Rules\Environment
 */
class ValidAliasTagFormat implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!preg_match('/^[a-z0-9_]+$/', $value)) {
            $fail('Nur a-z, 0-9 und Unterstrich');
        }
    }

}
