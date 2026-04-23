<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class EnvironmentRuleExists
 * @package App\Rules
 */
class EnvironmentRuleExists implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!class_exists('App\\Rules\\Environment\\' . $value)) {
            $fail('Die Klasse existiert nicht.');
        }
    }

}
