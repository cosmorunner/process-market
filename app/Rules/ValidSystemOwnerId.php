<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

/**
 * Class ValidSystemOwnerId
 * @package App\Rules
 */
class ValidSystemOwnerId implements ValidationRule {

    /**
     *  Entweder ist der Value die Id des Benutzers oder die Id einer Organisation wo der Benutzer
     *  das Recht "manageAccount" hat.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $user = Auth::user();
        $owner = $user->id === $value ? $user : Organisation::find($value);

        if (!$owner instanceof User && !$owner instanceof Organisation) {
            $fail($this->message());
        }

        if ($owner instanceof User) {
            return;
        }

        $organisationRole = $owner->roleOf($user);

        if (!($organisationRole instanceof Role && $organisationRole->can('platforms.manage'))) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Ungültige Daten.';
    }
}
