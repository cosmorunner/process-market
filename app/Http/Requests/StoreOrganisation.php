<?php

namespace App\Http\Requests;

use App\Rules\ValidNamespaceIdentifierFormat;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Anlegen einer neuen Organisation
 * Class StoreOrganisation
 * @package App\Http\Requests
 */
class StoreOrganisation extends FormRequest {

    /**
     * @return array
     */
    public function rules() {
        return [
            'name' => ['required', 'string', 'min:3', 'max:30'],
            'namespace' => ['required', 'string', 'min:3', 'max:20', new ValidNamespaceIdentifierFormat, 'unique:organisations,namespace', 'unique:users,namespace'],
            'description' => ['nullable', 'string', 'max:500']
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes() {
        return [
            'namespace' => 'Namespace'
        ];
    }
}
