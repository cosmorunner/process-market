<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\ProcessType\RelationType;
use App\Rules\Environment\ValidAliasTagFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreRelationType
 * @package App\Rules\ProcessType
 */
class StoreRelationType implements ValidationRule {

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
        $usedReferences = $this->definition->relationTypes->filter(fn(RelationType $relationType) => $relationType->reference !== null)
            ->pluck('reference');

        $validator = Validator::make((array) $value, [
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:200'],
            'connection_type' => ['required', Rule::In(['1-1', '1-n', 'n-1', 'n-n'])],
            'reference' => ['required', 'string', 'max:80', new ValidAliasTagFormat, Rule::notIn($usedReferences)]
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
