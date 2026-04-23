<?php

namespace App\Http\Requests;

use App\Rules\NotYetMemberOfOrganisation;
use App\Rules\UniqueOrganisationInvitation;
use App\Rules\UserHasNotBeenDeleted;
use App\Rules\ValidOrganisationRole;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreOrganisationInvitation
 * @package App\Http\Requests
 */
class StoreOrganisationInvitation extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        return [
            'email' => [
                'bail',
                'email',
                new NotYetMemberOfOrganisation(request('email')),
                new UniqueOrganisationInvitation,
                new UserHasNotBeenDeleted(request('email'))
            ],
            'role' => ['required', 'uuid', 'exists:roles,id', new ValidOrganisationRole],
        ];
    }
}
