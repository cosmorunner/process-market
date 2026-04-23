<?php

namespace App\Rules\Environment;

use App\Environment\Connector;
use App\Models\Environment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateVariable
 * @package App\Rules\Environment
 */
class UpdateVariable implements ValidationRule {

    private Environment $environment;

    /**
     * UpdateVariable constructor.
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
        $variableIds = $this->environment->blueprint->variables->pluck('id');

        /* @var Connector $currentVariable */
        $currentVariable = $this->environment->blueprint->variables->firstWhere('id', '=', $value['id'] ?? '');
        $variableIdentifiers = $this->environment->blueprint->variables->pluck('identifier');

        if ($currentVariable) {
            $variableIdentifiers = $variableIdentifiers->filter(fn($identifer) => $identifer != $currentVariable->identifier);
        }

        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid', Rule::in($variableIds)],
            'identifier' => ['required', 'string', 'max:60', Rule::notIn($variableIdentifiers)],
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
