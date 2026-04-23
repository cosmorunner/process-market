<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Checks if the provided context uuid is valid for the user.
 * Class ValidContextSwitch
 * @package App\Rules
 */
class ValidContextSwitch implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value either null or organisation id
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $user = auth()->user();
        if ($value === null) {
            return;
        }

        if (!$user->organisations->pluck('id')->contains($value)) {
            $fail('Benutzer ist nicht Mitglied der Organisation.');
        }
    }

}