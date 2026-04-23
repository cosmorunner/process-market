<?php

namespace App\Http\Requests;

use App\Rules\DemoPlatformDoesNotExist;
use App\Rules\UserDoesNotHavePlatform;
use App\Rules\ValidPlatformIdentifierFormat;
use App\Traits\UsesFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreAllisaPlatform
 * @package App\Http\Requests
 */
class StoreAllisaPlatform extends FormRequest {

    use UsesFailedValidationJsonResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'identifier' => ['bail', 'required', 'string', 'max:30', 'min:3', new ValidPlatformIdentifierFormat, new UserDoesNotHavePlatform, new DemoPlatformDoesNotExist],
            'first_name' => ['required', 'string', 'max:40'],
            'last_name' => ['required', 'string', 'max:40'],
            'terms' => ['accepted']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() {
        return [
            'terms.accepted' => 'Bitte aktivieren Sie die Checkbox.'
        ];
    }
}
