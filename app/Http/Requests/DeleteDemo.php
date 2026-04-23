<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DeleteDemo
 * @package App\Http\Requests
 */
class DeleteDemo extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'ref' => ['nullable', 'string']
        ];
    }
}
