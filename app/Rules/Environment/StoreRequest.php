<?php

namespace App\Rules\Environment;

use App\Models\Environment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreRequest
 * @package App\Rules\Environment
 */
class StoreRequest implements ValidationRule {

    private Environment $environment;

    /**
     * StoreRequest constructor.
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
        $requestIdentifiers = $this->environment->blueprint->requests->pluck('identifier');
        $connectorIds = $this->environment->blueprint->connectors->pluck('id');

        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid', Rule::notIn($requestIds)],
            'connector_id' => ['required', 'uuid', Rule::in($connectorIds)],
            'name' => ['required', 'string', 'max:60'],
            'description' => ['string', 'max:200'],
            'identifier' => ['required', 'string', 'max:60', Rule::notIn($requestIdentifiers)],
            'uri' => ['nullable', 'string', 'url'],
            'method' => ['nullable', 'string', 'in:GET,POST,PATCH,DELETE'],
            'active' => ['nullable', 'boolean'],
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
