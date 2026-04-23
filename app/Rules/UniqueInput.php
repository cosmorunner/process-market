<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

/**
 * Class UniqueInput
 * @package App\Rules
 */
class UniqueInput implements ValidationRule {

    private Collection $inputs;

    /**
     * Create a new rule instance.
     * @param Collection $inputs
     */
    public function __construct(Collection $inputs) {
        $this->inputs = $inputs;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if ($this->inputs->pluck('name')->contains($value)) {
            $fail('Ein Input mit diesem Namen existiert bereits.');
        }
    }

}
