<?php

namespace App\Rules\ProcessType;

use App\Rules\ValidPermissionIdent;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class StoreRole
 * @package App\Rules\ProcessType
 */
class StoreRole implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $validator = Validator::make((array) $value, [
            'name' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:200'],
            'permissions' => ['array'],
            'permissions.*' => ['array', new ValidPermissionIdent]
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
