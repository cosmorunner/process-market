<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePassword
 * @package App\Http\Requests
 */
class UpdatePassword extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'current_password' => 'required',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return array
     */
    public function messages() {
        return [
            'current_password.required' => 'Geben Sie ihr aktuelles Passwort ein.',
            'password.required' => 'Wählen Sie ein neues Passwort.',
            'password.min' => 'Das neue Passwort muss mindestens 8 Zeichen lang sein.',
            'password_confirmation.required' => 'Wiederholen Sie ihr neues Passwort.',
            'password_confirmation.same' => 'Die Passwort-Bestätigung stimmt nicht mit ihrem neuen Passwort überein.'
        ];
    }
}
