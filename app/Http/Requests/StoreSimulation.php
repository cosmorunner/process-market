<?php

namespace App\Http\Requests;

use App\Interfaces\HandlesDummyFiles;
use App\Models\ProcessVersion;
use App\Models\User;
use App\Rules\SimulationRoleAvailable;
use App\Rules\ValidEnvironment;
use App\Traits\UsesDummyFiles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Starten einer neuen Simulation.
 * Class StoreSimulation
 * @package App\Http\Requests
 */
class StoreSimulation extends FormRequest implements HandlesDummyFiles {

    use UsesDummyFiles;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        /* @var ProcessVersion $processVersion */
        $processVersion = request('processVersion');

        /* @var User $user */
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
            'environment_id' => ['nullable', 'uuid', Rule::in($environmentIds), new ValidEnvironment($processVersion)],
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

}
