<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class DeleteStatusType
 * @package App\Rules\ProcessType
 */
class DeleteStatusType implements ValidationRule {

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
    private $statusTypeIds;

    public function __construct() {
        $this->processVersion = request('processVersion');
        $this->definition = $this->processVersion->definition;
        $this->statusTypeIds = $this->definition->statusTypes->pluck('id');
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
            'id' => ['bail', 'required', 'string', 'uuid', Rule::in($this->statusTypeIds)],
        ], [], [
            'id' => 'Status',
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
