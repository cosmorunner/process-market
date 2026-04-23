<?php

namespace App\Rules\ProcessType;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class UpdateProcessTypeUnique
 * @package App\Rules\ProcessType
 */
class UpdateProcessTypeUnique implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $validator = Validator::make((array) $value, [
            'type' => ['bail', 'required', 'in:meta_data,process_data'],
            'data' => ['bail', 'nullable']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
