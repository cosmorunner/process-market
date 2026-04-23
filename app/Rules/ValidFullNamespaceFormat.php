<?php

namespace App\Rules;

use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidFullNamespaceFormat
 * @package App\Rules
 */
class ValidFullNamespaceFormat implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!Definition::validNamespace($value)) {
            $fail('Ungültiges Namespace Format.');
        }
    }

}
