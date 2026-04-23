<?php

namespace App\Rules\Environment;

use App\Models\Environment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreVariable
 * @package App\Rules\Environment
 */
class StoreVariable implements ValidationRule {

    private Environment $environment;

    /**
     * StoreVariable constructor.
     */
    public function __construct() {
        $this->environment = request('environment');
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $variableIds = ($this->environment->blueprint->variables ?? collect())->pluck('id');
        $variableIdentifiers = ($this->environment->blueprint->variables ?? collect())->pluck('identifier');

        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid', Rule::notIn($variableIds)],
            'identifier' => [
                'required',
                'string',
                'max:60',
                new ValidAliasTagFormat,
                Rule::notIn($variableIdentifiers)
            ],
            'type' => ['required', 'string'],
            'value' => ['nullable'],
            'is_public' => ['boolean']
        ], [], [
            'identifier' => 'Identifier'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
