<?php

namespace App\Rules\Environment;

use App\Environment\Request;
use App\Models\Environment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateRequest
 * @package App\Rules\Environment
 */
class UpdateRequest implements ValidationRule {

    private Environment $environment;

    /**
     * UpdateRequest constructor.
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
        $requestIds = $this->environment->blueprint->requests->pluck('id');
        $connectorIds = $this->environment->blueprint->connectors->pluck('id');

        /* @var Request $currentRequest */
        $currentRequest = $this->environment->blueprint->requests->firstWhere('id', '=', $value['id'] ?? '');
        $requestIdentifiers = $this->environment->blueprint->requests->pluck('identifier');

        if ($currentRequest) {
            $requestIdentifiers = $requestIdentifiers->filter(fn($identifer) => $identifer != $currentRequest->identifier);
        }

        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid', Rule::in($requestIds)],
            'connector_id' => ['required', 'uuid', Rule::in($connectorIds)],
            'name' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:200'],
            'identifier' => ['required', 'string', 'max:60', Rule::notIn($requestIdentifiers)],
            'uri' => ['required', 'string'],
            'method' => ['required', 'string', 'in:GET,POST,PATCH,DELETE'],
            'active' => ['nullable', 'boolean', 'accepted'],
            'bindings' => ['nullable', 'array'],
            'headers' => ['nullable', 'array'],
            'body' => ['nullable', 'array'],
            'validation' => ['nullable', 'array'],
            'debug_options' => ['nullable', 'array']
        ], [], [
            'identifier' => 'Identifier'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
