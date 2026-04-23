<?php

namespace App\Rules\ProcessType;

use App\ProcessType\Definition;
use App\Rules\UniqueActionTypeReference;
use App\Rules\ValidReference;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

/**
 * Class StoreActionType
 * @package App\Rules\ProcessType
 */
class StoreActionType implements ValidationRule {

    /**
     * @var Definition
     */
    private Definition $definition;

    public function __construct() {
        $processVersion = request('processVersion');
        $this->definition = $processVersion->definition;
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
            'name' => ['required', 'string', 'max:100'],
            'reference' => [
                'nullable',
                'string',
                'max:100',
                new ValidReference,
                new UniqueActionTypeReference($this->definition->actionTypes)
            ],
            'description' => ['nullable', 'string', 'max:200'],
            'category_id' => ['nullable', 'string', 'uuid'],
            'image' => ['nullable', 'string'],
            'save_label' => ['nullable', 'string', 'max:30'],
            'instant' => ['boolean'],
            'show_save_button' => ['boolean']
        ], [], ['category_id' => 'Kategorie']);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
