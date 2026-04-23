<?php

namespace App\Rules;

use App\ProcessType\State;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidStatusValues
 * @package App\Rules
 */
class ValidStatusValues implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        foreach ((array) $value as $item) {
            try {
                State::validateValue($item);
            }
            catch (Exception) {
                $fail('Ungültige Werte. Format z.B. 7, 1.275 oder -50.375');
            }
        }
    }

}
