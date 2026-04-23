<?php

namespace App\Rules\ProcessType;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class PasteElement
 * @package App\Rules\ProcessType
 */
class PasteElement implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $validator = Validator::make((array) $value, [
            'name' => ['required', 'string'],
            'options' => ['nullable', 'array']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
