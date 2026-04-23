<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Die eingegebene Version muss mindestens so hoch sein wie die bisherige Version des Graphen.
 * Class HigherVersionNumber
 * @package App\Rules
 */
class HigherVersionNumber implements ValidationRule {

    /**
     * @var string A valid version number, such as "1.0.0" or "2.34.5".
     */
    private $version;

    /**
     * Create a new rule instance.
     * @param string $version
     */
    public function __construct($version) {
        $this->version = $version;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->version) {
            $fail('Bitte geben Sie die Version ein.');
        }

        if (!version_compare($value, $this->version, '>')) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        $parts = explode('.', $this->version);
        $lastPart = (int) ($parts[2]);
        $lastPart++;
        $nextVersion = implode('.', [$parts[0], $parts[1], $lastPart]);

        return 'Bitte geben Sie mindestens die Version "' . $nextVersion . '" ein.';
    }
}
