<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * The last organisation administrator cannot be changed to a non-administrator role.
 * Class LastOrganisationOwnerCannotChangeToOtherRole
 * @package App\Rules
 */
class LastOrganisationOwnerCannotChangeToOtherRole implements ValidationRule {

    private ?Organisation $organisation;

    private ?User $user;

    /**
     * @return void
     */
    public function __construct() {
        $this->organisation = request()->route('organisation');
        $this->user = request()->route('user');
    }

    /**
     * Does not allow owners to change there role using the selection.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {

        // Owner should not be able to change their role.
        if ($this->organisation->roleOf($this->user)->isOwner()) {
            $fail('Die Organisation benötigt einen Eigentümer.');
        }
    }

}
