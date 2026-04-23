<?php

namespace App\Rules\ProcessType;

use App\Traits\UsesBulkErrorFormatting;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class StoreStatusType
 * @package App\Rules\ProcessType
 */
class StoreStatusTypeBulk implements ValidationRule {

    use UsesBulkErrorFormatting;

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
            'value' => ['nullable', 'array'],
            'value.*' => ['array', new StoreStatusType]
        ], [], [
            'value' => 'Einträge',
        ]);

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

            return [
                'name' => $rowParts[0] ?? '',
                'default' => empty($rowParts[1]) ? '-1' : $rowParts[1]
            ];
        })->toArray();

        return $value;
    }

}
