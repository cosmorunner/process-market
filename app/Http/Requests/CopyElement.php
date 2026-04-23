<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CopyElement
 * @package App\Http\Requests
 */
class CopyElement extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        return [
            'name' => ['required', 'string'],
            'object' => ['required', 'array'],
        ];
    }
}
