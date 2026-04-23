<?php

namespace App\Rules;

use App\Models\Organisation;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class NotYetMemberOfOrganisation
 * @package App\Rules
 */
class NotYetMemberOfOrganisation implements ValidationRule {

    private ?Organisation $organisation;

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
        $this->organisation = request()->route('organisation');
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        // Falls der Benutzer noch nicht im System existiert.
        $user = User::whereEmail($this->email)->first();

        if (!$user instanceof User) {
            return;
        }

        if ($this->organisation->isMember($user)) {
            $fail('Dieser Benutzer ist bereits Mitglied der Organisation.');
        }
    }

}
