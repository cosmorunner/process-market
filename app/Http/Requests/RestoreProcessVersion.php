<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RestoreProcessVersion
 * @package App\Http\Requests
 */
class RestoreProcessVersion extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'accept' => ['bail', 'required', 'in:on']
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function messages() {
        return [
            'accept.required' => 'Bestätigen Sie die Wiederherstellung, indem Sie die Checkbox aktivieren.'
        ];
    }
}
