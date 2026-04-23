<?php

namespace App\Rules\Environment;

use App\Models\Environment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreRelation
 * @package App\Rules\Environment
 */
class StoreRelation implements ValidationRule {

    private Environment $environment;

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
        // Standard-Allisa-Prozess-Id hinzufügen damit auf Verknüpfungen zur Demo-Prozess-Instanz hinzugefügt werden können.
        $processIds = $this->environment->blueprint->processes->pluck('id')
            ->add(config('allisa.simulation.process_id'))
            ->add(config('allisa.simulation.user_identity_id'));

        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid'],
            'left' => ['required', 'uuid', 'different:right', Rule::in($processIds)],
            'relation_type' => ['required', 'string'],
            'relation_type_name' => ['required', 'string'],
            'right' => ['required', 'uuid', Rule::in($processIds)],
            'data' => ['array']
        ], [], [
            'left' => 'Linker Prozess',
            'relation_type' => 'Verknüpfungstyp',
            'right' => 'Rechter Prozess',
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
