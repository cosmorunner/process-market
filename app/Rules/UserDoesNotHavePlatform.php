<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserDoesNotHavePlatform
 * @package App\Rules
 */
class UserDoesNotHavePlatform implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $user = Auth::user();

        if (!$user) {
            $fail($this->message());
        }

        if ($user->hasDemo()) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Sie haben bereits eine Allisa Plattform Demo erhalten.';
    }
}
