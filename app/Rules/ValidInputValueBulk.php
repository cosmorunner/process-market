<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

/**
 * Class ValidInputValueBulk
 * @package App\Rules
 */
class ValidInputValueBulk implements ValidationRule {

    /**
     * All action type inputs
     */
    private Collection $inputs;

    /**
     * Create a new rule instance.
     */
    public function __construct(Collection $inputs) {
        $this->inputs = $inputs;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $parts = array_map('trim', explode(';', $value));
        $name = $parts[0];

        if (isset($parts[2])) {
            $fail('Es wurde mehr Attribute für eine Zeile angegeben, als erwartet. Maximal erwartete Attribute: 2');

            return;
        }

        if (isset($parts[1]) && $parts[1] !== '#') {
            $validator = Validator::make((array) $parts[1], [
                'parts' => ['nullable', new InputOutputValueLength],
            ]);

            if (!$validator->passes()) {
                $fail($validator->messages());

                return;
            }
        }

        if (str_starts_with($name, '=')) {
            $name = substr($name, 1);
        }
        else if (str_starts_with($name, '~')) {
            $name = substr($name, 1);
        }

        $validator = Validator::make(['name' => $name], [
            'name' => ['required', 'string', 'max:60', new UniqueInput($this->inputs), new ValidInputOutputName],
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }
}
