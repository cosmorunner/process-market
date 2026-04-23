<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\ProcessType\StatusType;
use App\Rules\MaxEqualOrGreaterThanMin;
use App\Rules\MinEqualOrLowerThanMax;
use App\Rules\NoEncasingStates;
use App\Rules\NoOverlappingStates;
use App\Rules\StatusValueFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreState
 * @package App\Rules\ProcessType
 */
class StoreState implements ValidationRule {

    /**
     * @var ProcessVersion
     */
    private $processVersion;

    /**
     * @var string
     */
    private $statusTypeId;

    /**
     * @var Definition
     */
    private Definition $definition;

    /**
     * StatusTyp bei dem ein Zustand hinzugefügt wird.
     * @var StatusType
     */
    private $statusType;

    /**
     * Existing statustype ids
     * @var array
     */
    private $statusTypeIds;

    public function __construct(array $payload) {
        $this->processVersion = request('processVersion');
        $this->statusTypeId = $payload['status_type_id'] ?? '';
        $this->definition = $this->processVersion->definition;
        $this->statusTypeIds = $this->definition->statusTypes->pluck('id');
        $this->statusType = $this->definition->statusType($this->statusTypeId) ?? StatusType::make();
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
            'status_type_id' => ['bail', 'required', 'string', 'uuid', Rule::in($this->statusTypeIds)],
            'description' => ['bail', 'required', 'string', 'max:200'],
            'min' => [
                'bail',
                'filled',
                Rule::requiredIf(fn() => isset($value['max'])),
                new StatusValueFormat,
                new MinEqualOrLowerThanMax($value['max'] ?? $value['min'] ?? null),
                new NoOverlappingStates($this->statusType->states),
            ],
            'max' => [
                'bail',
                'filled',
                new StatusValueFormat,
                new MaxEqualOrGreaterThanMin($value['min'] ?? $value['max'] ?? null),
                new NoOverlappingStates($this->statusType->states),
                new NoEncasingStates($this->statusType->states, $value['min'] ?? null)
            ],
            'image' => ['nullable', 'string'],
            'color' => ['nullable', 'string']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
