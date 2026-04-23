<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\Rules\Environment\ValidAliasTagFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\NotIn;

/**
 * Class StoreEvent
 * @package App\Rules\ProcessType
 */
class StoreEvent implements ValidationRule {

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
        $usedNames = $this->definition->events->pluck('name')->toArray();

        $validator = Validator::make((array)$value, [
            'name' => ['required', 'string', 'max:40', new NotIn($usedNames), new ValidAliasTagFormat],
            'description' => ['nullable', 'string', 'max:200'],
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
