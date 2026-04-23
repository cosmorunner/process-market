<?php

namespace App\Http\Requests;

use App\Rules\UniqueUrl;
use App\Rules\ValidSystemOwnerId;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Hinzufügen eines neuen Allisa Systems.
 * Class StoreSystem
 * @package App\Http\Requests
 */
class StoreSystem extends FormRequest {

    /**
     * @return array
     */
    public function rules() {
        return [
            'name' => ['required', 'string', 'min:3', 'max:30'],
            'url' => ['required', 'string', 'url', new UniqueUrl],
            'client_id' => ['required', 'uuid', 'size:36'],
            'client_secret' => ['required', 'string', 'size:40'],
            'owner_id' => ['required', 'string', 'uuid', new ValidSystemOwnerId]
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes() {
        return [
            'client_id' => 'Client-Id',
            'client_secret' => 'Client-Secret',
        ];
    }
}
