<?php

namespace App\Rules\ProcessType;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class StoreMenuItem
 * @package App\Rules\ProcessType
 */
class StoreMenuItem implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $validator = Validator::make((array) $value, [
            'label' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'string', 'max:32'],
            'route_name' => ['nullable', 'string', 'max:50'],
            'sort' => ['integer'],
            'url' => ['required', 'string'],
            'target' => ['string', 'in:_self,_blank,popup']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
