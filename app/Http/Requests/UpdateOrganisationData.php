<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateOrganisationData
 * @package App\Http\Requests
 */
class UpdateOrganisationData extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => ['required', 'string', 'min:3', 'max:30'],
            'description' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:50'],
            'link' => ['bail','nullable', 'string', 'url', 'max:200']
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes() {
        return [
            'link' => 'Website'
        ];
    }
}
