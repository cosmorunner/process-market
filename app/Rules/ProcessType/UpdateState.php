<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\ProcessType\State;
use App\ProcessType\StatusType;
use App\Rules\MaxEqualOrGreaterThanMin;
use App\Rules\MinEqualOrLowerThanMax;
use App\Rules\NoOverlappingStates;
use App\Rules\StatusValueFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateState
 * @package App\Rules\ProcessType
 */
class UpdateState implements ValidationRule {

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
     * Existierende StatusTyp-ids
     * @var Collection
     */
    private $statusTypeIds;

    /**
     * StatusTyp bei dem ein Zustand aktualisiert wird.
     * @var StatusType
     */
    private $statusType;

    /**
     * Zustand-Ids aller Zustände des Statustyps.
     * @var Collection
     */
    private $stateIds;

    public function __construct(array $payload) {
        $this->processVersion = request('processVersion');
        $this->statusTypeId = $payload['status_type_id'] ?? '';
        $this->definition = $this->processVersion->definition;
        $this->statusTypeIds = $this->definition->statusTypes->pluck('id');
        $this->statusType = $this->definition->statusType($this->statusTypeId) ?? StatusType::make();
        $this->stateIds = $this->statusType ? $this->statusType->states->pluck('id') : collect();
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $otherStates = $this->statusType->states->filter(fn(State $state) => $state->id !== request('payload.id'));

        $validator = Validator::make((array) $value, [
            'id' => ['bail', 'required', 'uuid', Rule::in($this->stateIds)],
            'status_type_id' => ['bail', 'required', 'string', 'uuid', Rule::in($this->statusTypeIds)],
            'description' => ['bail', 'required', 'string', 'max:200'],
            'min' => [
                'bail',
                'required_if:hasCustomValue,true',
                'string',
                new StatusValueFormat,
                new MinEqualOrLowerThanMax(request('payload.max')),
                new NoOverlappingStates($otherStates)
            ],
            'max' => [
                'bail',
                'required_if:hasValueRange,true',
                'string',
                new StatusValueFormat,
                new MaxEqualOrGreaterThanMin(request('payload.min')),
                new NoOverlappingStates($otherStates)
            ],
            'image' => ['nullable', 'string'],
            'color' => ['nullable', 'string']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }
}
