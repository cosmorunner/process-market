<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

/**
 * Class UniqueOutput
 * @package App\Rules
 */
class UniqueOutput implements ValidationRule {

    private Collection $outputs;

    /**
     * Create a new rule instance.
     * @param Collection $outputs
     */
    public function __construct(Collection $outputs) {
        $this->outputs = $outputs;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if ($this->outputs->pluck('name')->contains($value)) {
            $fail('Ein Output mit diesem Namen existiert bereits.');
        }
    }
}
