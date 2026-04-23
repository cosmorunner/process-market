<?php

namespace App\Rules;

use App\ConsoleConnector;
use Closure;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class DemoPlatformDoesNotExist
 * @package App\Rules
 */
class DemoPlatformDoesNotExist implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     * @throws GuzzleException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $connector = new ConsoleConnector();
        if ($connector->platformExists($value)) {
            $fail('Es existiert bereits eine Allisa Platform mit diesem Namen');
        }
    }

}
