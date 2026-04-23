<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Ein anderer Administrator kann nicht von der Organisation entfernt werden.
 * Class CannotDeleteOtherAdministrator
 * @package App\Rules
 */
class OnlyOwnerCanDeleteAdministrator implements ValidationRule {

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
            $fail('Sie können andere Administratoren nicht von der Organisation entfernen.');
        }

        $authRole = $this->organisation->roleOf(auth()->user());

        if ($this->user->id !== auth()->user()->id && !$authRole->isOwner()) {
            $fail('Sie können andere Administratoren nicht von der Organisation entfernen.');
        }

    }

}
