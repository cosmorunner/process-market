<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RestoreProcess
 * @package App\Http\Requests
 */
class RestoreProcess extends FormRequest {

    /**
     * The checkbox must be acccepted
     * @return array
     */
    public function rules() {
        return [
            'accept' => ['bail', 'required', 'in:on']
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * @return array
     */
    public function messages() {
        return [
            'accept.required' => 'Bestätigen Sie die Anfrage, indem Sie die Checkbox aktivieren.'
        ];
    }
}
