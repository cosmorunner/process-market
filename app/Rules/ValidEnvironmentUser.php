<?php

namespace App\Rules;

use App\Models\Simulation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidEnvironmentUser
 * @package App\Rules
 */
class ValidEnvironmentUser implements ValidationRule {

    private ?Simulation $simulation;

    /**
     * Create a new rule instance.
     */
    public function __construct() {
        $this->simulation = request()->route('simulation');
    }

    /**
     * Prüfen ob die Benutzer Id dem Standard-Allisa-Benutzer oder einem Benutzer des genutzten Environments entspricht.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->simulation->environment) {
            $fail($this->message());
        }

        if (config('allisa.simulation.user_id') === $value) {
            return;
        }

        if (!$this->simulation->environment->blueprint->users->pluck('id')->contains($value)) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Ungültige Benutzer-Id oder fehlendes Environment.';
    }
}
