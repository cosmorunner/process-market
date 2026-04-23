<?php

namespace App\Http\Requests;

use App\Interfaces\HandlesDummyFiles;
use App\Models\License;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\User;
use App\Rules\SimulationRoleAvailable;
use App\Traits\UsesDummyFiles;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * Starten einer neuen Simulation.
 * Class StoreSimulation
 * @package App\Http\Requests
 */
class StoreProcessLicenseSimulation extends FormRequest implements HandlesDummyFiles {

    use UsesDummyFiles;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        /* @var License $license */
        /* @var User $user */
        /* @var Process $process */
        /* @var ProcessVersion $process */
        $license = request('license');
        $version = request('version');
        $process = $license->resource;
        $processVersion = $process?->version($version, true);

        if (!$process instanceof Process || !$processVersion instanceof ProcessVersion) {
            abort(Response::HTTP_BAD_REQUEST);
        }

        $user = auth()->user();
        $environmentIds = $processVersion->environments->pluck('id');
        $organisationIds = $user->organisations->pluck('id');

        $environment = $processVersion->environments()->where('id', $this->request->get('environment_id'))->first();
        $users = collect(['demo@example.com']);
        if ($environment) {
            $users = $users->concat($environment->blueprint->users->pluck('email'));
        }

        return [
            'action_data' => ['nullable', 'array'],
            'action_data.*' => ['nullable'],
            'role_id' => ['nullable', new SimulationRoleAvailable($processVersion)],
            'environment_id' => ['nullable', 'uuid', Rule::in($environmentIds)],
            'user_email' => ['nullable', 'string', Rule::in($users)],
            'organisation_id' => ['nullable', 'uuid', Rule::in($organisationIds)],
            'license_id' => ['nullable', 'exists:licenses,id'],
            'ref' => ['nullable', 'string']
        ];
    }

    /**
     * Die {{}}-Syntaxen, z.B. bei {{dummy.file.pdf}}, müssen durch ihre tatsächlichen Dummy-Werte ersetzt werden.
     * @return array
     */
    public function validationData() {
        $validationData = parent::validationData();
        $data = $this->insertsDummyFiles($validationData['action_data'] ?? []);

        return array_merge($validationData, ['action_data' => $data]);
    }

    /**
     * Get custom attributes for validator errors.
     * @return array
     */
    public function attributes() {
        return [
            'role_id' => 'Rolle'
        ];
    }

    /**
     * Handle a failed validation attempt.
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message' => 'Keine gültige Rolle angegeben. Legen Sie eine Standard/Öffentliche Rolle fest oder wählen Sie ggfls. eine Rolle.',
            'errors' => $validator->errors()
        ], 422));
    }

}
