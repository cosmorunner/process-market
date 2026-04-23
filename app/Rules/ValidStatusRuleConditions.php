<?php

namespace App\Rules;

use App\ProcessType\State;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Ramsey\Uuid\Uuid;

/**
 * Class ValidStatusRuleConditions
 * @package App\Rules
 */
class ValidStatusRuleConditions implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        foreach ((array) $value as $item) {
            // z.B. $item = ['1.000', 'group_1', '[[action.outputs.field]]', '=', '2.000']
            if (count($item) !== 5) {
                $fail($this->message());
            }

            try {
                $value = $item[0];
                if (!(Uuid::isValid($value) ? true : State::validateValue($value))) {
                    $fail($this->message());
                }
            }
            catch (Exception) {
                $fail($this->message());
            }
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Ungültige Konditionen.';
    }
}
