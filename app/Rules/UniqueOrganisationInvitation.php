<?php

namespace App\Rules;

use App\Models\Organisation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class UniqueOrganisationInvitation
 * Prüft ob eine Organisation-Einladung bereits mit der E-Mail Adresse existiert.
 * @package App\Rules
 */
class UniqueOrganisationInvitation implements ValidationRule {

    private ?Organisation $organisation;

    /**
     * Create a new rule instance.
     * @return void
     */
    public function __construct() {
        $this->organisation = request()->route('organisation');
    }

    /**
     * Prüfen ob es bereits eine Gruppen-Einladung zu der E-Mail Adresse gibt.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->organisation instanceof Organisation) {
            $fail($this->message());
        }

        if ($this->organisation->invitations->firstWhere('email', '=', $value) !== null) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Der Benutzer hat bereits eine ausstehende Einladung.';
    }
}
