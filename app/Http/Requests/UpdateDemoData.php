<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateDemoData
 * @package App\Http\Requests
 */
class UpdateDemoData extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'demo_data' => ['array'],
            'demo_data.*' => ['array'],
            'demo_data.*.action_type_id' => ['required', 'string', 'uuid'],
            'demo_data.*.name' => ['required', 'string'],
            'demo_data.*.values' => ['array'],
        ];
    }
}
