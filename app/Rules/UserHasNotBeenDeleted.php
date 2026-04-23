<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class UserHasNotBeenDeleted
 * @package App\Rules
 */
class UserHasNotBeenDeleted implements ValidationRule {

    /**
     * @var string|null
     */
    private ?string $email;

    /**
     * Create a new rule instance.
     * @param $email
     */
    public function __construct($email) {
        $this->email = $email;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!User::onlyTrashed()->where('email', '=', $this->email)->doesntExist()) {
            $fail('Dieser Benutzer existiert nicht mehr.');
        }
    }

}
