<?php

namespace App\Rules\ProcessType;

use App\ProcessType\State;
use App\ProcessType\StatusType;
use App\Traits\UsesBulkErrorFormatting;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class StoreState
 * @package App\Rules\ProcessType
 */
class StoreStateBulk implements ValidationRule {

    use UsesBulkErrorFormatting;

    /**
     * @var string
     */
    private $statusTypeId;

    public function __construct(array $payload) {
        $this->statusTypeId = $payload['status_type_id'] ?? '';
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $payload = $this->reformatToIndividualPayloads($value);

        $validator = Validator::make($payload, [
            'value' => [
                'nullable',
                'array',
            ],
            'value.*' => ['array', new StoreState(['status_type_id' => $this->statusTypeId])],
        ], [], [
            'value' => 'Einträge',
        ])->after(function (\Illuminate\Validation\Validator $validator) use ($payload) {
            // After the above validation, we additionally check the overlapping of states
            // within the provided bulk rows
            $isOverlapping = $this->hasOverlappingBulkRows($payload['value']);

            if ($isOverlapping) {
                $validator->errors()
                    ->add('value', 'Wertebereich überschneiden sich. Jeder Zustand muss einen einzigartigen Wertebereich haben.');
            }
        });

        if (!$validator->passes()) {
            $fail($this->reformatErrorMessages($validator->messages()));
        }
    }

    /**
     * The payload of the bulk command is reformatted to an array of payloads for the
     * indivudual payloads of the corresponding non-bulk command.
     * E.g. StoreStateBulk payload -> multiple StoreState paylods
     * @param mixed $value
     * @return array
     */
    private function reformatToIndividualPayloads(mixed $value): array {
        $value['value'] = collect($value['value'])->map(function ($rowString) {
            $rowParts = collect(explode(';', $rowString))->map(fn($item) => trim($item, ' '))->filter(fn($item) => $item);

            $payload = [
                'status_type_id' => $this->statusTypeId,
                'description' => $rowParts[0] ?? ''
            ];

            if (!empty($rowParts[1])) {
                $payload['min'] = $rowParts[1];
            }

            if (!empty($rowParts[2])) {
                $payload['max'] = $rowParts[2];
            }

            return $payload;
        })->toArray();

        return $value;
    }

    /**
     * Checks the provided bulk rows for overlapping min/max values.
     * @param array $payloadRows
     * @return bool
     */
    private function hasOverlappingBulkRows(array $payloadRows): bool {
        /* @var State[] $states */
        $states = collect($payloadRows)->filter(function ($item) {
            if (empty($item['min'])) {
                return false;
            }
            if (empty($item['max'])) {
                return State::isValidValueFormat($item['min']);
            }

            return State::isValidValueFormat($item['min']) && State::isValidValueFormat($item['max']);
        });

        foreach ($states as $outerKey => $outerState) {
            foreach ($states as $innerKey => $innerState) {
                $outerMax = $outerState['max'] ?? $outerState['min'];
                $innerMax = $innerState['max'] ?? $innerState['min'];

                if ($outerKey === $innerKey) {
                    continue;
                }
                if (in_array(bccomp($outerState['min'], $innerState['min'], StatusType::VALUE_PRECISION), [
                        0,
                        1
                    ]) && in_array(bccomp($outerMax, $innerMax, StatusType::VALUE_PRECISION), [0, -1])) {
                    return true;
                }
            }
        }

        return false;
    }

}