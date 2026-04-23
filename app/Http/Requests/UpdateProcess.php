<?php

namespace App\Http\Requests;

use App\Enums\Visibility;
use App\Models\Process;
use App\Rules\LicenseMustNotBePrivateWithPublicVisibility;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class UpdateProcess
 * @package App\Http\Requests
 */
class UpdateProcess extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        /* @var Process $process */
        $process = request('process');

        // Nur ein Prozess, der bereits eine fertiggestellte Version hat, kann öffentlich gemacht werden.
        if ($process->hasPublishedVersion()) {
            // Erst wenn Lizenzmanagement fertig ist
            $options = [Visibility::Private->value, Visibility::Hidden->value, Visibility::Public->value];
        }
        else {
            $options = [Visibility::Private->value];
        }

        return [
            'title' => ['nullable', 'string', 'min:3'],
            'description' => ['nullable', 'string', 'max:1000'],
            'visibility' => ['bail', 'required', 'in:' . implode(',', $options), new LicenseMustNotBePrivateWithPublicVisibility],
            'tags' => ['nullable', 'array', 'max:3'],
            'tags.*' => ['string', 'max:20'],
            'license_options' => ['array'],
            'license_options.*.level' => ['bail', 'required', 'in:private,open-source,no-license'],
            'license_options.*.level_options' => ['array'],
            'accept_license' => ['bail', 'accepted_if:license_options.0.level,open-source']
        ];
    }

    /**
     * @return array
     */
    public function attributes() {
        return [
            'visibility' => 'Sichtbarkeit',
            'accept_license' => 'Lizenzbedingung'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() {
        return [
            'accept_license.accepted_if' => 'Bitte akzeptieren Sie die Lizenzbedingungen'
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Ungültige Daten',
            'errors' => $validator->errors()
        ], 422));
    }
}
