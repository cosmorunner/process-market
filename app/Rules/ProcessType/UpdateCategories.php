<?php

namespace App\Rules\ProcessType;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class UpdateCategories
 * @package App\Rules\ProcessType
 */
class UpdateCategories implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $validator = Validator::make((array) $value, [
            'items' => ['array'],
            'items.*' => [new UpdateCategory]
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
