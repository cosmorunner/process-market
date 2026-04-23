<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\Role;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidOrganisationRole
 * @package App\Rules
 */
class ValidOrganisationRole implements ValidationRule {

    private ?Organisation $organisation;

    /**
     * Create a new rule instance.
     */
    public function __construct() {
        $this->organisation = request('organisation');
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->organisation->roles->firstWhere('id', '=', $value) instanceof Role) {
            $fail('Ungültige Rolle.');
        }
    }

}
