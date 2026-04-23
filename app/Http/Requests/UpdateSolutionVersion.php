<?php

namespace App\Http\Requests;

use App\Rules\ValidFullNamespaceFormat;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateSolutionVersion
 * @package App\Http\Requests
 */
class UpdateSolutionVersion extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        return [
            'data' => ['array'],
            'data.process_types' => ['array'],
            'data.process_types.*' => ['string', new ValidFullNamespaceFormat]
        ];
    }
}
