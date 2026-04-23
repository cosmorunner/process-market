<?php

namespace App\Rules\Environment;

use App\Models\Organisation;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreProcess
 * @package App\Rules\Environment
 */
class StoreProcess implements ValidationRule {

    /**
     * Namespaces (mit Version) auf denen der Benutzer Zugriff hat.
     */
    private Collection $namespaces;

    /**
     * StoreProcess constructor.
     */
    public function __construct() {
        /* @var User $user */
        $user = auth()->user();
        $accessibleProcessVersions = $user->latestPublishedProcessVersions();

        /* @var Organisation $organisation */
        foreach ($user->organisations as $organisation) {
            /** @noinspection PhpParamsInspection */
            $accessibleProcessVersions = $accessibleProcessVersions->concat($organisation->latestPublishedProcessVersions());
        }

        $fullNamespacesWithVersion = $accessibleProcessVersions->map(fn(ProcessVersion $processVersion) => $processVersion->full_namespace);

        $this->namespaces = $fullNamespacesWithVersion->concat($accessibleProcessVersions->pluck('process')
            ->map(fn(Process $process) => $process->full_namespace . '@latest'))->unique();
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:60'],
            'process_type' => ['required', 'string', Rule::in($this->namespaces)],
            'initial_data' => ['array'],
            'initial_situation' => ['array'],
            'accesses' => ['array']
        ], [], [
            'process_type' => 'Prozesstyp'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
