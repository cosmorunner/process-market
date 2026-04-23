<?php

namespace App\Rules;

use App\ProcessType\State;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidStatusTypDefaultValue
 * @package App\Rules
 */
class ValidStatusTypDefaultValue implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        try {
            State::validateValue($value);
        }
        catch (Exception) {
            $fail('Ungültiger Initial-Wert. Format z.B. 7, 1.275 oder -50.375');
        }
    }

}
