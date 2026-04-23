<?php

namespace App\Rules;

use App\Models\Process;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidRequestedProcessLicense
 * @package App\Rules
 */
class ValidRequestedProcessLicense implements ValidationRule {

    /**
     * Wert mit dem verglichen wird.
     */
    private ?Process $process;

    /**
     * Create a new rule instance.
     */
    public function __construct() {
        $processId = request('process_id');
        $this->process = Process::find($processId);
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$this->process?->validLicenseRequest($value)) {
            $fail('Die Lizenz-Anfrage ist ungültig.');
        }
    }

}
