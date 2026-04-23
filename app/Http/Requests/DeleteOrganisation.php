<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Organisation auflösen
 * Class DeleteOrganisation
 * @package App\Http\Requests
 */
class DeleteOrganisation extends FormRequest {

    /**
     * Die Checkbox muss akzeptiert werden.
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
