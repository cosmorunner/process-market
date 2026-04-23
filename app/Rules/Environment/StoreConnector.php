<?php

namespace App\Rules\Environment;

use App\Models\Environment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreConnector
 * @package App\Rules\Environment
 */
class StoreConnector implements ValidationRule {

    private Environment $environment;

    /**
     * StoreConnector constructor.
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
        $connectorIds = $this->environment->blueprint->connectors->pluck('id');
        $connectorIdentifiers = $this->environment->blueprint->connectors->pluck('identifier');

        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid', Rule::notIn($connectorIds)],
            'name' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:200'],
            'identifier' => ['required', 'string', 'max:60', Rule::notIn($connectorIdentifiers)],
            'type' => ['required', 'string', 'in:http,sftp,soap,database'],
            'base_uri' => ['nullable', 'string', 'url'],
            'mode' => ['nullable', 'string', 'in:debug'],
            'active' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array']

        ], [], [
            'identifier' => 'Identifier'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
