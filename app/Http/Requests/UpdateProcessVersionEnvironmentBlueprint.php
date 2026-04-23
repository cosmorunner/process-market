<?php

namespace App\Http\Requests;

use App\Rules\EnvironmentRuleExists;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class UpdateProcessVersionEnvironmentBlueprint
 * @package App\Http\Requests
 */
class UpdateProcessVersionEnvironmentBlueprint extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        $ruleClass = 'App\\Rules\\Environment\\' . request('command');

        $payload = request('payload') ?? [];
        $customRule = new $ruleClass($payload);
        $customRule->validate('payload', $payload, function ($messages) {
            throw new HttpResponseException(response()->json([
                'message' => 'Ungültige Daten.',
                'errors' => $messages
            ], 422));
        });

        return [
            'command' => ['bail', 'required', new EnvironmentRuleExists],
            'payload' => ['required', 'array'],
        ];
    }

    /**
     * Bei fehlgeschlagener Validierung sollen die Fehlermeldungen aus der "ruleClass" zurückgegeben werden,
     * damit es im Vue einheitlich ist (setErrors-Vuex-Action).
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Ungültige Daten.',
            'errors' => $validator->errors()->getMessages() ?? []
        ], 422));
    }
}
