<?php

namespace App\Rules\Environment;

use App\Models\Environment;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateUser
 * @package App\Rules\Environment
 */
class UpdateUser implements ValidationRule {

    private Environment $environment;

    /**
     * Namespaces (mit Version) auf denen der Benutzer Zugriff hat.
     */
    private Collection $namespaces;

    /**
     * Gültige Gruppen-Ids
     * @var Collection
     */
    private Collection $validGroupIds;

    /**
     * UpdateUser constructor.
     */
    public function __construct() {
        $this->environment = request('environment');

        /* @var User $user */
        $user = auth()->user();
        $processVersions = $user->accessiblePublishedProcessVersions();

        $fullNamespacesWithVersion = $processVersions->map(fn(ProcessVersion $processVersion) => $processVersion->full_namespace);

        $this->namespaces = $fullNamespacesWithVersion->concat($processVersions->pluck('process')
            ->map(fn(Process $process) => $process->full_namespace . '@latest'))
            ->add('allisa/person@latest')
            ->collect()
            ->unique();

        // Gültige Gruppen-Ids
        $this->validGroupIds = collect([
            ...$this->environment->blueprint->groups->pluck('id')->toArray(),
            config('allisa.simulation.default_group_id')
        ]);
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        // Allisa Standard-Gruppen-Id, "Direktor"-Gruppen-Rolle-Id und Standard System-Rolle Id müssen im .env eingetragen sein.
        if (!config('allisa.simulation.default_group_id') || !config('allisa.simulation.default_director_role_id') || !config('allisa.simulation.default_system_role_id')) {
            $fail('Ungültige Command-Eingabedaten.');
        }

        $currentUserId = $value['id'] ?? '';
        $users = $this->environment->blueprint->users->reject(function ($user) use ($currentUserId) {
            return $user->id === $currentUserId;
        });

        $existingUserAliases = $users->pluck('aliases')->flatten()->unique();
        $existingUserEmails = $users->pluck('email')->flatten()->unique();
        $existingProcessInstances = $this->environment->blueprint->processes->pluck('id')->flatten()->unique();

        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid', Rule::in($this->environment->blueprint->users->pluck('id'))],
            'first_name' => ['required', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:20'],
            'identity_process_type' => [
                'required_without:identity_process_instance',
                'nullable',
                'string',
                Rule::in($this->namespaces)
            ],
            'identity_process_instance' => [
                'required_without:identity_process_type',
                'nullable',
                'string',
                Rule::in($existingProcessInstances)
            ],
            'email' => ['required', 'email', Rule::notIn($existingUserEmails)],
            'group_id' => ['nullable', 'uuid', Rule::in($this->validGroupIds)],
            'aliases' => ['array', 'required'],
            'aliases.*' => ['required', 'string', new ValidAliasTagFormat, Rule::notIn($existingUserAliases)],
            'tags' => ['array'],
            'tags.*' => ['required', 'string', new ValidAliasTagFormat],
        ], [
            'email.required' => 'Keinen Benutzer ausgewählt.'
        ], [
            'identity_process_type' => 'Prozess-Identität',
            'identity_process_instance' => 'Prozess-Identität-Instanz',
            'aliases.*' => 'Alias',
            'tags.*' => 'Tag'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
