<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\Rules\UniqueActionTypeReference;
use App\Rules\ValidReference;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateActionType
 * @package App\Rules\ProcessType
 */
class UpdateActionType implements ValidationRule {

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
            'id' => ['required', 'uuid', Rule::in($this->actionTypeIds)],
            'category_id' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:100'],
            'reference' => [
                'required',
                'string',
                'max:100',
                new ValidReference,
                new UniqueActionTypeReference($this->definition->actionTypes, $value['id'])
            ],
            'description' => ['nullable', 'string', 'max:200'],
            'image' => ['nullable', 'string'],
            'save_label' => ['nullable', 'string', 'max:30'],
            'instant' => ['boolean'],
            'show_save_button' => ['boolean'],
            'components' => ['array'],
            'action_rules' => ['array'],
            'status_rules' => ['array'],
            'inputs' => ['array'],
            'outputs' => ['array'],
            'processors' => ['array'],
            'visibility' => ['integer'],
            'sort' => ['required', 'integer'],
        ], [], [
            'name' => 'Name',
            'description' => 'Beschreibung',
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Ungültige Command-Eingabedaten.';
    }
}
