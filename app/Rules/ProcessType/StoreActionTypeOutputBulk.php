<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Output;
use App\Traits\UsesBulkErrorFormatting;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class StoreActionTypeOutputBulk
 * @package App\Rules\ProcessType
 */
class StoreActionTypeOutputBulk implements ValidationRule {

    use UsesBulkErrorFormatting;

    /**
     * @var ProcessVersion
     */
    private $processVersion;

    /**
     * @var Definition
     */
    private Definition $definition;

    /**
     * @var ActionType
     */
    private $actionType;

    /**
     * StoreActionTypeInputBulk constructor.
     */
    public function __construct() {
        $this->processVersion = request('processVersion');
        $this->definition = $this->processVersion->definition;
        $this->actionType = $this->definition->actionType(request('payload.action_type_id'));
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
            'value' => ['nullable', 'array'],
            'value.*' => ['array', new StoreActionTypeOutput]
        ], [], [
            'value' => 'Einträge'
        ])->after(function ($validator) use ($payload) {
            // After the above validation, we additionally check the uniqueness of given input names.
            $names = collect($payload['value'])->map(fn($item) => $item['name']);

            if ($names->duplicates()->isNotEmpty()) {
                $validator->errors()->add('value', 'Datenfeldnamen müssen einzigartig sein.');
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
            $type = Output::TYPE_BASIC;

            if (str_starts_with($rowParts[0] ?? '', '=')) {
                $type = Output::TYPE_ARRAY;
            }

            if (str_starts_with($rowParts[0] ?? '', '~')) {
                $type = Output::TYPE_OBJECT;
            }

            return [
                'action_type_id' => $this->actionType->id,
                'name' => trim($rowParts[0] ?? '', '=~!'),
                'value' => $rowParts[1] ?? '',
                'type' => $type,
                'validation' => str_ends_with($rowParts[0] ?? '', '!') ? ['required'] : ['nullable'],
                'type_options' => [],
                'include_in_process_data' => $rowParts[2] ?? '0',
                'include_in_input_data' => $rowParts[3] ?? '0',
                'load_process_data_field' => $rowParts[4] ?? '0',
                'create_form_field' => $rowParts[5] ?? '0',
            ];
        })->toArray();

        return $value;
    }

}
