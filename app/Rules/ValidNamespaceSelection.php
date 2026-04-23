<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Der gewählte Namespace muss entweder jenem des Benutzers entsprechen oder einer der Organisation
 * zu denen der Benutzer Zugriff hat.
 * Class ValidNamespaceSelection
 * @package App\Rules
 */
class ValidNamespaceSelection implements ValidationRule {

    /**
     * @var User;
     */
    private $user;

    /**
     * ValidNamespaceSelection constructor.
     */
    public function __construct() {
        $this->user = auth()->user();
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $userNamespace = $this->user->namespace;
        $organisationNamespaces = $this->user->organisations->pluck('namespace');
        $validNamespaces = collect([$userNamespace, ...$organisationNamespaces]);

        if (!$validNamespaces->contains($value)) {
            $fail('Ungültiger Namespace');
        }
    }

}
