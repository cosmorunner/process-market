<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class GetSyntaxValues
 * @package App\Http\Requests
 */
class GetSyntaxValues extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        return [
            'syntax_parts' => ['array'],
            'pipe_parts' => ['array'],
            'action_type_id' => ['nullable', 'string'],
            'search' => ['nullable', 'string']
        ];
    }
}
