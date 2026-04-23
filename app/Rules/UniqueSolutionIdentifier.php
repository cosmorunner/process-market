<?php

namespace App\Rules;

use App\Models\Solution;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class UniqueSolutionIdentifier
 * @package App\Rules
 */
class UniqueSolutionIdentifier implements ValidationRule {

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
        if (!Solution::whereFullNamespace($this->namespace . '/' . $value)->doesntExist()) {
            $fail('Lösung-Identifikation existiert bereits oder es besteht eine archivierte Lösung.');
        }
    }

}
