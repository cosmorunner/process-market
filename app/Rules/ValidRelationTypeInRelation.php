<?php

namespace App\Rules;

use App\Environment\Process;
use App\Models\Environment;
use App\Models\ProcessVersion;
use App\ProcessType\RelationType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Prüft ob der angegebene Verknüpfungstyp vom "left"-Prozess bei einer Relation in einem Environment existiert.
 * Class ValidRelationTypeInRelation
 * @package App\Rules
 */
class ValidRelationTypeInRelation implements ValidationRule {

    private string $leftProcessId;

    private Environment $environment;

    private ProcessVersion $processVersion;

    /**
     * Create a new rule instance.
     * @param $leftProcessId
     * @param $environment
     * @param ProcessVersion $processVersion Aktueller Graph der bearbeitet wird.
     */
    public function __construct($leftProcessId, $environment, ProcessVersion $processVersion) {
        $this->leftProcessId = $leftProcessId;
        $this->environment = $environment;
        $this->processVersion = $processVersion;
    }

    /**
     * Prüft ob ein angegebener Verknüpfungstyp bei einem Relation existiert.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if ($this->leftProcessId === config('allisa.simulation.process_id')) {
            if (!$this->processVersion->definition->relationType($value) instanceof RelationType) {
                $fail($this->message());
            }

            return;
        }

        $process = $this->environment->blueprint->processes->firstWhere('id', '=', $this->leftProcessId);

        if (!$process instanceof Process) {
            $fail($this->message());
        }

        $versionOfEnvironmentProcess = ProcessVersion::findByFullNamespace($process->process_type);

        if (!$versionOfEnvironmentProcess instanceof ProcessVersion) {
            $fail($this->message());
        }

        if (!$versionOfEnvironmentProcess->definition->relationType($value) instanceof RelationType) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Ungültiger Verknüpfungstyp.';
    }
}
