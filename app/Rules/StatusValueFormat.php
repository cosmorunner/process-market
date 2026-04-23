<?php

namespace App\Rules;

use App\ProcessType\State;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class StatusValueFormat
 * @package App\Rules
 */
class StatusValueFormat implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        try {
            $value = State::validateValue($value);
        }
        catch (Exception) {
            $fail('Ungültiger Wert für einen Status.');
        }

        if (!$value) {
            $fail('Ungültiger Wert für einen Status.');
        }
    }

}
