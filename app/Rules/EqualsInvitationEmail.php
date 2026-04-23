<?php

namespace App\Rules;

use App\Models\Invitation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Ramsey\Uuid\Uuid;

/**
 * Wenn einer Einladung gefolgt wird, muss bei der Registrierung dieselbe E-Mail Adresse wie jene aus der Einladung
 * genutzt werden.
 * Class EqualsInvitationEmail
 * @package App\Rules
 */
class EqualsInvitationEmail implements ValidationRule {

    private ?string $invitationId;

    /**
     * @param $invitationId
     */
    public function __construct($invitationId) {
        $this->invitationId = $invitationId;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (is_null($this->invitationId) || !Uuid::isValid($this->invitationId)) {
            return;
        }
        $invitation = Invitation::find($this->invitationId);

        if (!($invitation instanceof Invitation && $value === $invitation->email)) {
            $fail('Die E-Mail Adresse muss mit jener aus der Einladung übereinstimmen.');
        }
    }

}
