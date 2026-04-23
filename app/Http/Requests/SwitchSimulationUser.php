<?php

namespace App\Http\Requests;

use App\Rules\ValidEnvironmentUser;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Wechseln des Benutzers eine Simulation.
 * Class StoreSystem
 * @package App\Http\Requests
 */
class SwitchSimulationUser extends FormRequest {

    /**
     * @return array
     */
    public function rules() {
        return [
            'user_id' => ['required', 'uuid', new ValidEnvironmentUser],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes() {
        return [
            'user_id' => 'Benutzer-Id'
        ];
    }
}
