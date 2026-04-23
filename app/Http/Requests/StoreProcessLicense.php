<?php

namespace App\Http\Requests;

use App\Rules\ReceiverDoesNotHaveLicense;
use App\Rules\ValidLicenseReceiver;
use App\Rules\ValidRequestedLicense;
use App\Traits\UsesFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Erhalten einer Lizenz.
 * Class StoreProcessLicense
 * @package App\Http\Requests
 */
class StoreProcessLicense extends FormRequest {

    use UsesFailedValidationJsonResponse;

    /**
     * @return array
     */
    public function rules() {
        return [
            'accept' => ['accepted'],
            'resource_id' => ['bail', 'required', 'string', 'uuid', 'exists:processes,id'],
            'resource_type' => ['bail', 'required', 'string'],
            'license' => ['bail', 'required', 'array', new ValidRequestedLicense],
            'receiver' => ['bail', 'required', 'string', new ValidLicenseReceiver, new ReceiverDoesNotHaveLicense],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * @return array
     */
    public function attributes() {
        return [
            'accept' => 'Lizenzbedingung',
            'receiver' => 'Empfänger der Lizenz'
        ];
    }
}
