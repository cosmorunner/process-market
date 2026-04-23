<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class UpdateSolutionLicense
 * @package App\Http\Requests
 */
class UpdateSolutionLicense extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'level' => ['required', 'in:private,open-source'],
            'level_options' => ['array'],
        ];
    }

    /**
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
