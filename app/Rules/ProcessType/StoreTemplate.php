<?php

namespace App\Rules\ProcessType;

use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreTemplate
 * @package App\Rules\ProcessType
 */
class StoreTemplate implements ValidationRule {

    private Definition $definition;

    /**
     * StoreTemplate constructor.
     */
    public function __construct() {
        $this->definition = request()->route('processVersion')->definition;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $templateNames = $this->definition->templates->pluck('name');

        $validator = Validator::make((array) $value, [
            'name' => ['required', 'string', 'max:80', Rule::notIn($templateNames)],
            'template' => ['required', 'uuid', 'exists:templates,id']
        ], [
            'name.not_in' => 'Dieser Name ist bereits vergeben.'
        ], [
            'template' => 'Vorlage'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
