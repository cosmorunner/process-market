<?php

namespace App\Rules;

use App\Models\ProcessVersion;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidEnvironment
 * @package App\Rules
 */
class ValidEnvironment implements ValidationRule {

    private ProcessVersion $processVersion;

    /**
     * SimulationRoleAvailable constructor.
     * @param ProcessVersion $processVersion
     */
    public function __construct(ProcessVersion $processVersion) {
        $this->processVersion = $processVersion;
    }

    /**
     * Prüft ob die Demo-Umgebung ($value) Teil der Demo-Umgebungen ist, auf die der Benutzer Zugriff hat.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->processVersion->userEnvironments()->pluck('id')->contains($value)) {
            $fail('Ungültige Demo-Umgebung.');
        }
    }

}
