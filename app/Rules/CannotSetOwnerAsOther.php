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
class CannotSetOwnerAsOther implements ValidationRule {

    private ?Organisation $organisation;

    private ?User $user;

    private ?Role $role;

    /**
     * @return void
     */
    public function __construct() {
        $this->organisation = request()->route('organisation');
        $this->user = request()->route('user');
        $this->role = Role::whereId(request('role'))->first();
    }

    /**
     * Nur erlauben wenn es noch mindestens 2 Administratoren gibt.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $userRole = $this->organisation->roleOf($this->user);

        // Admins should not be able to set the owner role
        if (!$userRole->isOwner() && $this->role->isOwner()) {
            $fail('Sie haben keine Berechtigung diese Role zu vergeben.');
        }
    }

}
