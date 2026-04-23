<?php

namespace App\Http\Requests;

use App\Rules\ValidContextSwitch;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for switching the context of the user. User can switch to personal or organisation context.
 * Checks whether the provided organisation context ist valid.
 * Class UpdateUserContext
 * @package App\Http\Requests
 */
class UpdateUserContext extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        return [
            'context' => ['nullable', 'uuid', new ValidContextSwitch],
        ];
    }
}