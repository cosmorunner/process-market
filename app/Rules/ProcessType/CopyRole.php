<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class CopyRole
 * @package App\Rules\ProcessType
 */
class CopyRole implements ValidationRule {

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
    private $roleIds;

    public function __construct() {
        $this->processVersion = request('processVersion');
        $this->definition = $this->processVersion->definition;
        $this->roleIds = $this->definition->roles->pluck('id');
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
            'id' => ['bail', 'required', 'string', 'uuid', Rule::in($this->roleIds)],
        ], [], [
            'id' => 'Role',
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }
}
