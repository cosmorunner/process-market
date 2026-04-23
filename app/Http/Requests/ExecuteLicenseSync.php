<?php

namespace App\Http\Requests;

use App\Models\License;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ExecuteLicenseSync
 * @package App\Http\Requests
 */
class ExecuteLicenseSync extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        /* @var License $license */
        $license = request('license');
        $systemIds = !$license ? collect() : $license->owner->systems->pluck('id');

        return [
            'system_ids' => ['required', 'array'],
            'system_ids.*' => ['required', 'uuid', Rule::in($systemIds)]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() {
        return [
            'system_ids.required' => 'Wählen Sie mindestens eine Allisa Plattform.',
            'system_ids.in' => 'Ungültige Allisa Plattform Angabe.'
        ];
    }
}
