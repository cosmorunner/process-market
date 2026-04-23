<?php

namespace App\Rules;

use App\Models\Invitation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Ramsey\Uuid\Uuid;

/**
 * Class ValidInvitation
 * @package App\Rules
 */
class ValidInvitation implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!Uuid::isValid($value)) {
            $fail($this->message());
        }

        $invitation = Invitation::find($value);
        if (!($invitation instanceof Invitation && $invitation->isValid())) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Die Einladung ist nicht mehr gültig.';
    }
}
