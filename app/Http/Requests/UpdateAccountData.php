<?php

namespace App\Http\Requests;

use App\Rules\NewEmail;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateAccountData
 * @package App\Http\Requests
 */
class UpdateAccountData extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email' => ['required', 'email', new NewEmail],
            'bio' => ['nullable', 'string', 'max:250'],
            'city' => ['nullable', 'string', 'max:50']
        ];
    }
}
