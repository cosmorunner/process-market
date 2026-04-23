<?php

namespace App\Http\Requests;

use App\Rules\ProcessTypeCommandRuleExists;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

/**
 * Validiert alle Änderungen der Prozesstyp-Definition. Die "METHOD" entscheidet welche Regel für die Validierung
 * genutzt wird.
 * Class UpdateDefinition
 * @package App\Http\Requests
 */
class UpdateDefinition extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        /* @var ValidationRule $customRule */
        $ruleClass = 'App\\Rules\\ProcessType\\' . request('command');

        if (!class_exists($ruleClass)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Ungültiger Command.' . (config('app.debug') ? ' Regel-Klasse für den Command fehlt.' : ''));
        }

        $payload = request('payload') ?? [];
        $customRule = new $ruleClass($payload);

        $customRule->validate('payload', $payload, function ($messages) {
            throw new HttpResponseException(response()->json([
                'message' => 'Ungültige Daten.',
                'errors' => $messages
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        });

        return [
            'position' => ['nullable', 'string', 'json'],
            'command' => ['bail', 'required', new ProcessTypeCommandRuleExists],
            'payload' => ['required', 'array'],
            'view_port' => ['nullable', 'array'],
            'view_port.x' => ['numeric'],
            'view_port.y' => ['numeric'],
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
