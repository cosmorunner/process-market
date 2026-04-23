<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\ProcessType\Output;
use App\Rules\InputOutputValueLength;
use App\Rules\ValidInputOutputName;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\NotIn;

/**
 * Class UpdateProcessTypeOutput
 * @package App\Rules\ProcessType
 */
class UpdateProcessTypeOutput implements ValidationRule {

    /**
     * @var ProcessVersion
     */
    private $processVersion;
    /**
     * @var Definition
     */
    private Definition $definition;

    public function __construct() {
        $this->processVersion = request('processVersion');
        $this->definition = $this->processVersion->definition;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $outputNames = $this->definition->outputs->pluck('name');

        $newName = $value['name'];
        $oldName = $value['old_name'];
        $validNames = collect();

        if ($oldName === $newName) {
            foreach ($outputNames as $validValue) {
                if ($validValue != $oldName) {
                    $validNames->add($validValue);
                }
            }
        }
        else {
            $validNames = $outputNames;
        }

        $validator = Validator::make((array) $value, [
            'name' => ['required', 'string', 'max:60', new NotIn($validNames->toArray()), new ValidInputOutputName],
            'old_name' => ['required', 'string', 'max:60', new In($outputNames->toArray()), new ValidInputOutputName],
            'description' => ['nullable', 'string', 'max:200'],
            'default' => ['nullable', new InputOutputValueLength],
            'type' => ['required', new In([Output::TYPE_BASIC, Output::TYPE_ARRAY, Output::TYPE_OBJECT])],
            'type_options' => ['array'],
        ], [], [
            'name' => 'Name',
            'description' => 'Beschreibung',
            'default' => 'Standard-Wert',
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
