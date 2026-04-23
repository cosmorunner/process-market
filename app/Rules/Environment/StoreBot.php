<?php

namespace App\Rules\Environment;

use App\Models\Environment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreBot
 * @package App\Rules\Environment
 */
class StoreBot implements ValidationRule {

    private Environment $environment;

    /**
     * StoreBot constructor.
     */
    public function __construct() {
        $this->environment = request('environment');
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

        $existingBotAliases = $this->environment->blueprint->bots->pluck('aliases')->flatten()->unique();

        $validator = Validator::make((array) $value, [
            'first_name' => ['required', 'string', 'max:20'],
            'aliases' => ['array', 'required'],
            'aliases.*' => ['required', 'string', new ValidAliasTagFormat, Rule::notIn($existingBotAliases)],
        ], [], [
            'aliases.*' => 'Alias'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
