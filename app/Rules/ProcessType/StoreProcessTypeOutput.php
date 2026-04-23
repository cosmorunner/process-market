<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
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
 * Class StoreProcessTypeOutput
 * @package App\Rules\ProcessType
 */
class StoreProcessTypeOutput implements ValidationRule {

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
        $validator = Validator::make((array) $value, [
            'name' => ['required', 'string', 'max:60', new UniqueOutput($this->definition->outputs), new ValidInputOutputName],
            'description' => ['nullable', 'string', 'max:200'],
            'default' => ['nullable', new InputOutputValueLength],
            'type' => ['required', new In([Output::TYPE_BASIC, Output::TYPE_ARRAY, Output::TYPE_OBJECT])],
            'type_options' => ['array'],
        ], [], [
            'name' => 'Name',
            'description' => 'Beschreibung',
            'default' => 'Standard-Wert'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
