<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ProcessTypeCommandRuleExists
 * @package App\Rules
 */
class ProcessTypeCommandRuleExists implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!class_exists('App\\Rules\\ProcessType\\' . $value)) {
            $fail('Die Command-Klasse existiert nicht.');
        }
    }

}
