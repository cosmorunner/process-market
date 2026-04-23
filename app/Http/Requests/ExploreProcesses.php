<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ExploreProcesses
 * @package App\Http\Requests
 */
class ExploreProcesses extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        $columns = ['title', 'description', 'namespace', 'identifier'];

        return [
            'items_per_page' => ['nullable', 'integer', 'min:1'],
            'tags' => ['nullable', 'array'],
            'search' => ['nullable', 'string'],
            'sort' => ['nullable', 'string', Rule::in($columns)]
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return array
     */
    public function messages() {
        return [];
    }
}
