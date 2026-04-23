<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Prüft ob das User-Model im Route-Model-Binding Mitglied in der Organisation im Route-Model-Binding ist.
 * Class ValidOrganisationMembership
 * @package App\Rules
 */
class ValidOrganisationMembership implements ValidationRule {

    private ?Organisation $organisation;

    private ?User $user;

    /**
     * Create a new rule instance.
     * @return void
     */
    public function __construct() {
        $this->organisation = request('organisation');
        $this->user = request('user');
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->organisation->roleOf($this->user) instanceof Role) {
            $fail('Benutzer ist nicht Mitglied der Organisation.');
        }
    }

}
