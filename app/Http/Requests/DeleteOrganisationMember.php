<?php

namespace App\Http\Requests;

use App\Rules\CannotDeleteLastOrganisationAdministrator;
use App\Rules\OnlyOwnerCanDeleteAdministrator;
use App\Rules\CannotDeleteSelfFromOrganisation;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DeleteOrganisationMember
 * @package App\Http\Requests
 */
class DeleteOrganisationMember extends FormRequest {

    /**
     * Die Checkbox muss akzeptiert werden.
     * @return array
     */
    public function rules() {
        return [
            'accept' => ['bail', 'required', 'in:on', new CannotDeleteSelfFromOrganisation, new OnlyOwnerCanDeleteAdministrator]
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function messages() {
        return [
            'accept.required' => 'Bestätigen Sie die Anfrage, indem Sie die Checkbox aktivieren.'
        ];
    }

}
