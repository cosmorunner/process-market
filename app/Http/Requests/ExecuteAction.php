<?php

namespace App\Http\Requests;

use App\Interfaces\HandlesDummyFiles;
use App\Rules\ValidActionType;
use App\Traits\UsesDummyFiles;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Ausführen einer Aktion in der Simulation.
 * Class ExecuteAction
 * @package App\Http\Requests
 */
class ExecuteAction extends FormRequest implements HandlesDummyFiles {

    use UsesDummyFiles;

    /**
     * @return array
     */
    public function rules() {
        return [
            'action_type_id' => ['required', new ValidActionType],
            'data' => ['array'],
            'data.*' => ['bail', 'nullable']
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Ungültige Daten-Eingabe.',
            'errors' => $validator->errors()
        ], 422));
    }

    /**
     * Die {{}}-Syntaxen, z.B. bei {{dummy.file.pdf}}, müssen durch ihre tatsächlichen Dummy-Werte ersetzt werden.
     *
     * @return array
     */
    public function validationData() {
        $validationData = parent::validationData();
        $data = $this->insertsDummyFiles($validationData['data'] ?? []);

        return array_merge($validationData, ['data' => $data]);
    }
}
