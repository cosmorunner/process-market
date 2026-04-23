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
 * Class StoreGroup
 * @package App\Rules\Environment
 */
class StoreGroup implements ValidationRule {

    private Environment $environment;

    private Collection $namespaces;

    /**
     * StoreGroup constructor.
     */
    public function __construct() {
        $this->environment = request('environment');

        /* @var User $user */
        $user = auth()->user();
        $processVersions = $user->accessiblePublishedProcessVersions();
        $fullNamespacesWithVersion = $processVersions->map(fn(ProcessVersion $processVersion) => $processVersion->full_namespace);

        $this->namespaces = $fullNamespacesWithVersion->concat($processVersions->pluck('process')
            ->map(fn(Process $process) => $process->full_namespace . '@latest'))
            ->add('allisa/organisation@latest')
            ->collect()
            ->unique();
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $existingGroupIds = $this->environment->blueprint->groups->pluck('id');
        $existingGroupAliases = $this->environment->blueprint->groups->pluck('aliases')->flatten()->unique();
        $existingProcessInstances = $this->environment->blueprint->processes->pluck('id')->flatten()->unique();

        $validator = Validator::make((array) $value, [
            'id' => ['nullable', 'uuid', Rule::notIn($existingGroupIds)],
            'name' => ['required', 'string', 'max:30'],
            'aliases' => ['array', 'required'],
            'aliases.*' => ['required', 'string', new ValidAliasTagFormat, Rule::notIn($existingGroupAliases)],
            'tags' => ['array'],
            'tags.*' => ['required', 'string', new ValidAliasTagFormat],
            'identity_process_type' => ['nullable', 'string', Rule::in($this->namespaces)],
            'identity_process_instance' => ['nullable', 'string', Rule::in($existingProcessInstances)],
        ], [], [
            'aliases.*' => 'Alias',
            'tags.*' => 'Tag'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
