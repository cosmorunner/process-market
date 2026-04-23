<?php

namespace App\Rules\ProcessType;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class StoreCategory
 * @package App\Rules\ProcessType
 */
class StoreCategory implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $validator = Validator::make((array) $value, [
            'name' => ['required', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'string'],
            'color' => ['nullable', 'string'],
            'locked' => ['boolean'],
            'hidden' => ['boolean']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
