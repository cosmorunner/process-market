<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * Starten einer neuen Lösung-Demo.
 * Class StoreDemo
 * @package App\Http\Requests
 */
class StoreDemo extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        /* @var User $user */
        $user = auth()->user();
        $organisationIds = $user->organisations->pluck('id');

        return [
            'ref' => ['nullable', 'string'],
            'organisation_id' => ['nullable', Rule::in($organisationIds)],
            'license_id' => ['nullable', 'exists:licenses,id']
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Ungültige Daten.',
            'errors' => $validator->errors()
        ], 422));
    }

}
