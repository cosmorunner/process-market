<?php

namespace App\Http\Requests;

use App\Rules\NoMissingProcessLicenseForSolutionLicense;
use App\Rules\ResourceVersionExists;
use App\Rules\ValidLicenseReceiver;
use App\Rules\ValidRequestedLicense;
use App\Rules\ValidVersionFormat;
use App\Traits\UsesFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Erhalten einer Lizenz.
 * Class StoreSolutionLicense
 * @package App\Http\Requests
 */
class StoreSolutionLicense extends FormRequest {

    use UsesFailedValidationJsonResponse;

    /**
     * @return array
     */
    public function rules() {
        return [
            'accept' => ['accepted'],
            'resource_id' => ['bail', 'required', 'string', 'uuid', 'exists:solutions,id'],
            'resource_version' => ['bail', 'required', 'string', new ValidVersionFormat, new ResourceVersionExists],
            'resource_type' => ['bail', 'required', 'string'],
            'license' => ['bail', 'required', 'array', new ValidRequestedLicense],
            'receiver' => ['bail', 'required', 'string', new ValidLicenseReceiver],
            'process_licenses' => ['bail', 'array', new NoMissingProcessLicenseForSolutionLicense],
            'process_licenses.*.full_namespace' => ['bail', 'required', 'exists:processes,full_namespace'],
            'process_licenses.*.license' => ['bail', 'array'],
            'process_licenses.*.license.level' => ['bail', 'in:open-source']
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes() {
        return [
            'accept' => 'Lizenzbedingung',
            'receiver' => 'Empfänger der Lizenz'
        ];
    }
}
