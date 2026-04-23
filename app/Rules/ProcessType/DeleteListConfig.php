<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class DeleteListConfig
 * @package App\Rules\ProcessType
 */
class DeleteListConfig implements ValidationRule {

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
        $listConfigs = $this->definition->listConfigs;

        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid', Rule::in($listConfigs->pluck('id'))],
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
