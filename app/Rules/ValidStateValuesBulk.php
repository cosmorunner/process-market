<?php

namespace App\Rules;

use App\Models\ProcessVersion;
use App\ProcessType\State;
use App\ProcessType\StatusType;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class ValidStateValueBulk
 * @package App\Rules
 */
class ValidStateValuesBulk implements ValidationRule {

    /**
     * Statustype where new states are created.
     * @var StatusType|null
     */
    private ?StatusType $statusType;

    /**
     * @param string $statusTypeId
     */
    public function __construct(string $statusTypeId) {
        /* @var ProcessVersion $processVersion */
        $processVersion = request('processVersion');
        $this->statusType = $processVersion->definition->statusType($statusTypeId);
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     * @throws Exception
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        // First, we check if the provided values (min/max) are valid,
        // and if they do not overlap existing states.
        foreach ($value as $row) {
            $parts = collect(explode(';', $row))->map(fn($item) => trim($item, ' '))->filter(fn($item) => $item);

            // If no max value is set, we set it to min value, or to null if neither is set.
            $row = [
                'description' => $parts[0],
                'min' => $parts[1] ?? null,
                'max' => $parts[2] ?? $parts[1] ?? null,
            ];

            // We accept nullable because it means, that the command will identify a valid min/max value for the state automatically.
            $validator = Validator::make($row, [
                'description' => ['bail', 'required', 'string', 'max:200'],
                'min' => [
                    'bail',
                    'filled',
                    Rule::requiredIf(fn() => request()->has('payload.max')),
                    new StatusValueFormat,
                    new MinEqualOrLowerThanMax(request('payload.max')),
                    new NoOverlappingStates($this->statusType->states)
                ],
                'max' => [
                    'bail',
                    'filled',
                    new StatusValueFormat,
                    new MaxEqualOrGreaterThanMin(request('payload.min')),
                    new NoOverlappingStates($this->statusType->states)
                ],
            ]);

            if (!$validator->passes()) {
                $fail('not working');

                return;
            }
        }

        // Lastly we need to check, if the provided values themselves do not overlap each other.
        // For example ["NewState1;1", "NewState2;1"] overlap and ["NewState1;5", "NewState2;4;6"] as well.

        // First we create a list of states.
        $fakeStates = collect();

        foreach ($value as $row) {
            $parts = collect(explode(';', $row))->map(fn($item) => trim($item, ' '))->filter(fn($item) => $item);
            $description = $parts[0];

            // We dont care about empty min values, as the command will identify a valid min/max value.
            if (!isset($parts[1]) || empty($parts[1])) {
                continue;
            }

            $min = $parts[1];
            $max = $parts[2] ?? $parts[1];

            // We push a new State objekt into $fakeStates.
            $fakeStates->push(State::make(['description' => $description, 'min' => $min, 'max' => $max]));
        }

        foreach ($fakeStates as $state) {
            $otherStates = $fakeStates->filter(fn(State $fakeState) => $fakeState->description !== $state->description);
            $values = [
                'description' => $state->description,
                'min' => $state->min,
                'max' => $state->max,
            ];

            $validator = Validator::make($values, [
                'description' => ['bail', 'required', 'string', 'max:200'],
                'min' => [
                    'bail',
                    'nullable',
                    new StatusValueFormat,
                    new MinEqualOrLowerThanMax($values['max']),
                    new NoOverlappingStates($otherStates)
                ],
                'max' => [
                    'bail',
                    'nullable',
                    new StatusValueFormat,
                    new MaxEqualOrGreaterThanMin($values['min']),
                    new NoOverlappingStates($otherStates)
                ],
            ]);

            if (!$validator->passes()) {
                $fail($validator->messages());
            }
        }
    }
}
