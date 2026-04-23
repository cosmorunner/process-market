<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class EnvironmentStoreProcess
 * @package App\Rules
 */
class EnvironmentStoreProcess implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $validator = Validator::make((array) $value, [
            'action_type_id' => ['required', 'uuid', Rule::in($this->actionTypeIds)],
            'identifier' => ['required', 'string'],
            'conditions' => ['array'],
            'options' => ['array']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
