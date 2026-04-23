<?php

namespace App\Http\Requests;

use App\Traits\UsesFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateSolutionConfig
 * @package App\Http\Requests
 */
class UpdateSolutionConfig extends FormRequest {

    use UsesFailedValidationJsonResponse;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        return [
            'name' => ['required', 'min:3'],
        ];
    }

    /**
     * @return array
     */
    public function attributes() {
        return [
            'visibility' => 'Sichtbarkeit',
            'accept_license' => 'Lizenzbedingung'
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return array
     */
    public function messages() {
        return [
            'accept_license.accepted_if' => 'Bitte akzeptieren Sie die Lizenzbedingungen'
        ];
    }
}
