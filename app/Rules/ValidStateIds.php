<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidStateIds
 * @package App\Rules
 */
class ValidStateIds implements ValidationRule {

    /**
     * Zustände die erlaubt sind.
     * @var array
     */
    private $stateIds;

    /**
     * Create a new rule instance.
     * @param array $stateIds
     */
    public function __construct(array $stateIds) {
        $this->stateIds = $stateIds;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        foreach ((array) $value as $item) {
            if (!in_array($item, $this->stateIds)) {
                $fail('Ungültige Zustände.');
            }
        }
    }

}
