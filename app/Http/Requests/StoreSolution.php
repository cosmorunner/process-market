<?php

namespace App\Http\Requests;

use App\Rules\UniqueSolutionIdentifier;
use App\Rules\ValidNamespaceIdentifierFormat;
use App\Rules\ValidNamespaceSelection;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class StoreSolution
 * @package App\Http\Requests
 */
class StoreSolution extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => ['required', 'min:3'],
            'namespace' => ['required', 'string', new ValidNamespaceIdentifierFormat, new ValidNamespaceSelection, 'min:3', 'max:20'],
            'identifier' => ['required', 'string', new ValidNamespaceIdentifierFormat, 'min:3', 'max:30', new UniqueSolutionIdentifier],
            'description' => ['nullable', 'string', 'max:1000'],
            'tags' => ['nullable', 'array', 'max:3'],
            'tags.*' => ['string', 'max:20']
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes() {
        return [
            'namespace' => 'Namespace',
            'identifier' => 'Identifikation'
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
            'message' => 'Ungültige Daten',
            'errors' => $validator->errors()
        ], 422));
    }
}
