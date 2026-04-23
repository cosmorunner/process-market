<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Output;
use App\Rules\InputOutputValueLength;
use App\Rules\UniqueOutput;
use App\Rules\ValidInputOutputName;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;

/**
 * Class StoreActionTypeOutput
 * @package App\Rules\ProcessType
 */
class StoreActionTypeOutput implements ValidationRule {

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
     * StoreActionTypeOutput constructor.
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
        if (!$this->actionType instanceof ActionType) {
            $fail('Ungültige Command-Eingabedaten.');
        }

        $validator = Validator::make((array) $value, [
            'name' => ['required', 'string', 'max:60', new UniqueOutput($this->actionType->outputs), new ValidInputOutputName],
            'description' => ['nullable', 'string', 'max:200'],
            'default' => ['nullable', new InputOutputValueLength],
            'validation' => ['nullable', 'array'],
            'type' => ['required', new In([Output::TYPE_BASIC, Output::TYPE_ARRAY, Output::TYPE_OBJECT])],
            'type_options' => ['array'],
            'include_in_process_data' => ['required', 'boolean'],
            'include_in_input_data' => ['required', 'boolean'],
            'load_process_data_field' => ['required', 'boolean'],
            'create_form_field' => ['required', 'boolean']
        ], [], [
            'name' => 'Name',
            'description' => 'Beschreibung',
            'default' => 'Standard-Wert',
            'validation' => 'Validierung',
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
