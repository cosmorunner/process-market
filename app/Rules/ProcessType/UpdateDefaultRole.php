<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateDefaultRole
 * @package App\Rules\ProcessType
 */
class UpdateDefaultRole implements ValidationRule {

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
            'default_role_id' => [
                'bail',
                'nullable',
                'uuid',
                Rule::in($this->definition->roles->pluck('id'))
            ],
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
