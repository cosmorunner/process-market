<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Der letzte Organisation-Administrator kann nicht entfernt werden.
 * Class CannotDeleteLastOrganisationAdministrator
 * @package App\Rules
 */
class CannotDeleteLastOrganisationAdministrator implements ValidationRule {

    private ?Organisation $organisation;

    private ?User $user;

    /**
     * @param User|null $user
     */
    public function __construct(User $user = null) {
        $this->organisation = request()->route('organisation');
        $this->user = $user ?? request()->route('user');
    }

    /**
     * Nur erlauben wenn es noch mindestens 2 Administratoren gibt.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->organisation instanceof Organisation || !$this->user instanceof User) {
            $fail('Die Organisation benötigt mindestens einen Administrator.');
        }

        $adminRole = $this->organisation->adminRole();
        $adminsCount = $this->organisation->membersOfRole($adminRole)->count();

        // Wenn versucht wird einen Administrator zu entfernen,
        // muss es noch mindestens einen weiteren Administrator geben.
        if ($this->organisation->roleOf($this->user)->isAdmin() && $adminsCount === 1) {
            $fail('Die Organisation benötigt mindestens einen Administrator.');
        }
    }

}
