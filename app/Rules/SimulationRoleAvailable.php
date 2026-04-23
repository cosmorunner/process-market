<?php

namespace App\Rules;

use App\Models\ProcessVersion;
use App\ProcessType\Role;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class SimulationRoleAvailable
 * @package App\Rules
 */
class SimulationRoleAvailable implements ValidationRule {

    private ProcessVersion $processVersion;

    /**
     * SimulationRoleAvailable constructor.
     * @param ProcessVersion $processVersion
     */
    public function __construct(ProcessVersion $processVersion) {
        $this->processVersion = $processVersion;
    }

    /**
     * Prüfen, ob die angegebene Rolle existiert oder eine Standardrolle
     * oder eine öffentliche Rolle angegeben wurde.
     * Nur bei eine Simulation mit Initial-Aktion muss keine Rolle angegeben werden.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $identifiedRole = $this->processVersion->definition->role($value) ?? $this->processVersion->definition->defaultRole ?? $this->processVersion->definition->publicRole;

        // Bei einer normalen Instantiierung muss eine Rolle angegeben sein oder
        // der Prozess eine Standard-Rolle oder öffentliche Rolle haben
        if (!$identifiedRole instanceof Role) {
            $fail('Keine Rolle für die Simulation angegeben.');
        }
    }

}
