<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateSetting
 * @package App\Http\Requests
 */
class UpdateSetting extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $names = ['hide-allisa-promotion'];

        return [
            'settingName' => ['bail', 'required', 'string', Rule::in($names)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() {
        return [];
    }
}
