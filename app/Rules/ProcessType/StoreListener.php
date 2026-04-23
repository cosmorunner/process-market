<?php

namespace App\Rules\ProcessType;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class StoreListener
 * @package App\Rules\ProcessType
 */
class StoreListener implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $validator = Validator::make((array) $value, [
            'events' => ['required', 'array'],
            'description' => ['nullable', 'string', 'max:200'],
            'relation_type' => ['nullable', 'string'],
            'conditions' => ['array'],
            'type' => ['required', 'string', 'in:execute_action'],
            'type_options' => ['array']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
