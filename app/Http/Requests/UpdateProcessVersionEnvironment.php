<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Aktualisieren einer Simulations-Umgebung.
 * Class UpdateProcessVersionEnvironment
 * @package App\Http\Requests
 */
class UpdateProcessVersionEnvironment extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:300'],
            'default' => ['required', 'boolean'],
            'default_user' => ['nullable', 'uuid'],
            'public' => ['nullable', 'boolean'],
            'initial_action_type_id' => ['nullable', 'uuid'],
            'query_context' => ['nullable', 'string'],
            'blueprint' => ['array'],
            'blueprint.users' => ['nullable', 'array'],
            'blueprint.processes' => ['nullable', 'array'],
            'blueprint.groups' => ['nullable', 'array'],
            'blueprint.group_accesses' => ['nullable', 'array'],
            'blueprint.group_roles' => ['nullable', 'array'],
            'blueprint.public_apis' => ['nullable', 'array'],
            'blueprint.bots' => ['nullable', 'array'],
            'blueprint.relations' => ['nullable', 'array'],
            'blueprint.connectors' => ['nullable', 'array'],
            'blueprint.requests' => ['nullable', 'array'],
            'blueprint.settings' => ['nullable', 'array'],
            'blueprint.system_accesses' => ['nullable', 'array'],
            'blueprint.variables' => ['nullable', 'array'],
            'blueprint.tasks' => ['nullable', 'array'],
        ];
    }

    /**
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Ungültige Daten.',
            'errors' => $validator->errors()->getMessages() ?? []
        ], 422));
    }
}
