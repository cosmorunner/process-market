<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

/**
 * Class NewEmail
 * @package App\Rules
 */
class NewEmail implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $loggedInEmail = Auth::user()->email;

        if (!($value === $loggedInEmail || User::whereEmail($value)->get()->isEmpty())) {
            $fail('Ungültige E-Mail.');
        }
    }

}
