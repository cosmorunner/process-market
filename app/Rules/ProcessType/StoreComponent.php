<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\Rules\ValidVersionFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreComponent
 * @package App\Rules\ProcessType
 */
class StoreComponent implements ValidationRule {

    /**
     * @var ProcessVersion
     */
    private $processVersion;

    /**
     * @var Definition
     */
    private Definition $definition;

    /**
     * @var array
     */
    private $actionTypeIds;

    public function __construct() {
        $this->processVersion = request('processVersion');
        $this->definition = $this->processVersion->definition;
        $this->actionTypeIds = $this->definition->actionTypes->pluck('id');
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
            'action_type_id' => ['bail', 'required', 'string', 'uuid', Rule::in($this->actionTypeIds)],
            'label' => ['nullable', 'string', 'max:40'],
            'css_classes' => ['nullable', 'string', 'max:250'],
            'namespace' => ['nullable', 'string', 'max:200'],
            'identifier' => ['nullable', 'string', 'max:200'],
            'sort' => ['nullable', 'numeric'],
            'version' => ['required', new ValidVersionFormat],
            'width' => ['required', 'numeric', 'max:12'],
            'options' => ['array']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
