<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\Input;
use App\Rules\InputOutputValueLength;
use App\Rules\ValidInputOutputName;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\NotIn;

/**
 * Class UpdateActionTypeInput
 * @package App\Rules\ProcessType
 */
class UpdateActionTypeInput implements ValidationRule {

    /**
     * @var ProcessVersion
     */
    private $processVersion;

    private Definition $definition;

    /**
     * @var ActionType
     */
    private $actionType;

    /**
     * UpdateActionTypeInput constructor.
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

        $inputNames = $this->actionType->inputs->pluck('name');

        $newName = $value['name'];
        $oldName = $value['old_name'];
        $validNames = collect();

        if ($oldName === $newName) {
            foreach ($inputNames as $validValue) {
                if ($validValue != $oldName) {
                    $validNames->add($validValue);
                }
            }
        }
        else {
            $validNames = $inputNames;
        }

        $validator = Validator::make((array) $value, [
            'name' => [
                'required',
                'string',
                'max:60',
                new NotIn($validNames->toArray()),
                new ValidInputOutputName
            ],
            'old_name' => [
                'required',
                'string',
                'max:60',
                new In($inputNames->toArray()),
                new ValidInputOutputName],
            'value' => ['nullable', new InputOutputValueLength],
            'type' => [
                'required',
                new In([Input::TYPE_AUTO, Input::TYPE_BASIC, Input::TYPE_ARRAY, Input::TYPE_OBJECT, Input::TYPE_LIST_CONFIG])
            ],
            'type_options' => ['array'],
        ], [], [
            'name' => 'Name',
            'value' => 'Wert',
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }
}
