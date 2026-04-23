<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class CannotDeleteSelfFromOrganisation
 * @package App\Rules
 */
class CannotDeleteSelfFromOrganisation implements ValidationRule {

    private ?User $user;

    public function __construct() {
        $this->user = request()->route('user');
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if ($this->user->id === auth()->user()->id) {
            $fail('Sie können sich selbst nicht von der Organisation entfernen.');
        }
    }

}
