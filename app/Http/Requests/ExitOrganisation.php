<?php

namespace App\Http\Requests;

use App\Rules\CannotDeleteLastOrganisationAdministrator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Aus Organisation austreten.
 * Class ExitOrganisation
 * @package App\Http\Requests
 */
class ExitOrganisation extends FormRequest {

    /**
     * The checkbox must be accepted.
     * @return array
     */
    public function rules() {
        return [
            'accept' => ['bail', 'required', 'in:on', new CannotDeleteLastOrganisationAdministrator(Auth::user())]
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
