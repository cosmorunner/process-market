<?php

namespace App\Http\Requests;

use App\Rules\CannotSetOwnerAsOther;
use App\Rules\LastOrganisationOwnerCannotChangeToOtherRole;
use App\Rules\ValidOrganisationMembership;
use App\Rules\ValidOrganisationRole;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateOrganisationMember
 * @package App\Http\Requests
 */
class UpdateOrganisationMember extends FormRequest {

    /**
     * Die Rolle eines Organisation-Mitglieds ändern.
     *
     * @return array
     */
    public function rules() {
        return [
            'role' => ['required', 'uuid', new ValidOrganisationRole, new ValidOrganisationMembership, new LastOrganisationOwnerCannotChangeToOtherRole, new CannotSetOwnerAsOther]
        ];
    }
}
