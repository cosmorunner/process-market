<?php

namespace App\Rules\Environment;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidSlugFormat
 * @package App\Rules\Environment
 */
class ValidSlugFormat implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!preg_match('/^[a-z0-9\-]+$/', $value)) {
            $fail('Nur a-z, 0-9 und Bindestrich');
        }
    }

}
