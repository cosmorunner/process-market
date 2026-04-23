<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreProcessVersion
 * @package App\Http\Requests
 */
class StoreProcessVersion extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        return [
            'targets' => ['array'],
            'targets.*.id' => ['string'],
            'targets.*.position' => ['nullable', 'array'],
            'targets.*.position.x' => ['numeric'],
            'targets.*.position.y' => ['numeric'],
            'targets.*.classes' => ['nullable', 'string'],
        ];
    }
}
