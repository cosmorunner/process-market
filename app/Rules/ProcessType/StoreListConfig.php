<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreListConfig
 * @package App\Rules\ProcessType
 */
class StoreListConfig implements ValidationRule {

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
            'name' => ['bail', 'required', 'string', 'max:64'],
            'description' => ['nullable', 'string', 'max:300'],
            'slug' => [
                'bail',
                'required',
                'string',
                Rule::notIn($this->definition->listConfigs->pluck('slug'))
            ],
            'template' => ['bail', 'nullable', 'string', Rule::in(array_keys(config('list_templates')))],
        ], [], [
            'slug' => 'Slug',
            'template' => 'Vorlage',
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
