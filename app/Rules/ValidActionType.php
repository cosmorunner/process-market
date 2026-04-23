<?php

namespace App\Rules;

use App\Models\Simulation;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Storage;

/**
 * Class ValidActionType
 * @package App\Rules
 */
class ValidActionType implements ValidationRule {

    /**
     * Alle Aktionstypen des ProzessTyps
     * @var Definition
     */
    private $definition;

    /**
     * Create a new rule instance.
     */
    public function __construct() {
        /* @var Simulation $simulation */
        $simulation = request('simulation');

        // Get the definition from the exported definition json file from the simulation
        $definitionJson = json_decode(Storage::get($simulation->definitionExportFilePath()), true);
        $this->definition = new Definition($definitionJson['definition']);
    }

    /**
     * Prüfen ob die übermittelte Aktionstyp-Id in dem Prozesstyp existiert.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->definition->actionType($value) instanceof ActionType) {
            $fail('Ungültige Aktionstyp-Id.');
        }
    }

}
