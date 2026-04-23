<?php

namespace App\Rules;

use App\Models\Process;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class UniqueProcessIdentifier
 * @package App\Rules
 */
class UniqueProcessIdentifier implements ValidationRule {

    /**
     * Gewählter Namespace
     * @var string
     */
    private string $namespace;

    public function __construct() {
        $this->namespace = request('namespace') ?? '';
    }

    /**
     * Prüft ob der gewählte Prozess-Identifier mit dem selektierten Namespace (Benutzer oder eine Gruppe) existiert.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!Process::whereFullNamespace($this->namespace . '/' . $value)->doesntExist()) {
            $fail('Prozess mit dieser Identifikation existiert bereits oder es besteht ein archivierter Prozess.');
        }
    }

}
