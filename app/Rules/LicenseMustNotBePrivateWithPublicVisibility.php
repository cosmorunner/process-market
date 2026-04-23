<?php

namespace App\Rules;

use App\Enums\Visibility;
use App\Models\License;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class LicenseMustNotBePrivate
 * Wenn die Prozess-Sichtbarkeit NICHT Privat ist, darf die Lizenz-Option NICHT privat sein.
 * @package App\Rules
 */
class LicenseMustNotBePrivateWithPublicVisibility implements ValidationRule {

    /**
     * @var string|null
     */
    private ?string $licenseLevel;

    /**
     * Create a new rule instance.
     * @return void
     */
    public function __construct() {

        $this->licenseLevel = request('license_options.0.level', License::LEVEL_PRIVATE);
    }

    /**
     * Prüfen ob es bereits eine Gruppen-Einladung zu der E-Mail Adresse gibt.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $visibility = $value;

        if ($visibility > Visibility::Private->value && $this->licenseLevel === 'private') {
            $fail('Bei öffentlicher Sichtbarkeit darf die Lizenz-Option nicht "Privat" sein.');
        }
    }

}
