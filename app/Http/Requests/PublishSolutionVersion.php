<?php

namespace App\Http\Requests;

use App\Models\SolutionVersion;
use App\Rules\HigherVersionNumber;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PublishSolutionVersion
 * @package App\Http\Requests
 */
class PublishSolutionVersion extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        /* @var SolutionVersion $solutionVersion */
        $solutionVersion = request('solutionVersion');
        $latestPublishedVersion = $solutionVersion->solution->latestPublishedVersion;
        $latestVersionNumber = $latestPublishedVersion !== null ? $latestPublishedVersion->version : '0.0.0';

        return [
            'version' => [
                'bail',
                'required',
                'string',
                'regex:/^(?:(\d+)\.)(?:(\d+)\.)(\d+)$/',
                new HigherVersionNumber($latestVersionNumber)
            ],
            'changelog' => ['nullable', 'sometimes', 'string']
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return array
     */
    public function messages() {
        return [
            'version.regex' => 'Bitte tragen Sie eine dreistellige Versionnummer ein, z.B. 0.0.9, 1.0.0 oder 1.3.7.'
        ];
    }
}
