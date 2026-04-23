<?php

namespace App\Rules;

use App\ProcessType\State;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidStatusValue
 * @package App\Rules
 */
class ValidStatusValue implements ValidationRule {

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
            $fail('Ungültiger Wert. Format z.B. 7, 1.275 oder -50.375');
        }
    }

}
