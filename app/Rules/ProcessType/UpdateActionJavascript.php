<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateActionJavascript
 * @package App\Rules\ProcessType
 */
class UpdateActionJavascript implements ValidationRule {
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
            'action_type_id' => ['required', 'uuid', Rule::in($this->actionTypeIds)],
            'javascript' => ['required', 'JSON']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
