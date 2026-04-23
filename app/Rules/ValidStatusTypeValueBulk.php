<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class ValidStatusTypeValueBulk
 * @package App\Rules
 */
class ValidStatusTypeValueBulk implements ValidationRule {

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */

    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $parts = array_map('trim', explode(';', $value));

        if (isset($parts[2])) {
            $fail("Es wurde mehr Attribute für eine Zeile angegeben, als erwartet. Maximal erwartet: 2");
            return;
        }

        $value = [
            'name' => $parts[0],
            'default' => empty($parts[1]) ? "0" : $parts[1]
        ];

        $validator = Validator::make($value, [
            'name' => ['required', 'string', 'max:50'],
            'default' => ['bail', 'required', 'string', new StatusValueFormat]
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }
}