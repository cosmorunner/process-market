<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\StatusRule;
use App\ProcessType\StatusType;
use App\Rules\ValidStatusRuleConditions;
use App\Rules\ValidStatusValues;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreStatusRule
 * @package App\Rules\ProcessType
 */
class StoreStatusRule implements ValidationRule {

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
     * @var array
     */
    private $statusTypeIds;

    /**
     * @var array
     */
    private $actionTypeIds;

    /**
     * @var StatusType
     */
    private $statusType;

    /**
     * @var ActionType
     */
    private $actionType;

    /**
     * @var array
     */
    private $operators = [
        StatusRule::OPERATOR_SET,
        StatusRule::OPERATOR_ADD,
        StatusRule::OPERATOR_SUB,
    ];

    public function __construct(array $payload) {
        $this->processVersion = request('processVersion');
        $this->statusTypeId = $payload['status_type_id'];
        $this->definition = $this->processVersion->definition;
        $this->statusType = $this->definition->statusType($this->statusTypeId);
        $this->statusTypeIds = $this->definition->statusTypes->pluck('id');
        $this->actionType = $this->definition->actionType(request('payload.action_type_id'));
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
        if (!$this->statusType instanceof StatusType || !$this->actionType instanceof ActionType) {
            $fail(['action_type_id' => ['Wählen Sie eine Aktion.']]);
        }

        // Für Smart-Status können keine Statusregeln erstellt werden, weil sich der Wert selbst automatisch setzt.
        if ($this->statusType->isSmartStatus()) {
            $fail(['action_type_id' => ['Es können keine Regeln zu einem Smart-Status hinzugefügt werden.']]);
        }

        if ($this->actionType->statusRule($this->statusTypeId) instanceof StatusRule) {
            $fail(['action_type_id' => ['Regel existiert bereits.']]);
        }

        $stateIds = $this->statusType->states->pluck('id');

        $validator = Validator::make((array) $value, [
            'status_type_id' => ['bail', 'required', 'string', 'uuid', Rule::in($this->statusTypeIds)],
            'action_type_id' => ['bail', 'required', 'string', 'uuid', Rule::in($this->actionTypeIds)],
            'operator' => ['bail', 'required', 'string', Rule::in($this->operators)],
            'output' => ['bail', 'nullable', 'required_without_all:values,conditions,state', 'string'],
            'state' => [
                'bail',
                'nullable',
                'required_without_all:values,conditions,output',
                'uuid',
                Rule::in($stateIds)
            ],
            'values' => [
                'bail',
                'nullable',
                'required_without_all:output,conditions,state',
                'array',
                'size:1',
                new ValidStatusValues
            ],
            'conditions' => [
                'bail',
                'nullable',
                'required_without_all:values,output,state',
                'array',
                new ValidStatusRuleConditions
            ]
        ], [], [
            'action_type_id' => 'Aktion',
            'status_type_id' => 'Status',
            'state' => 'Zustand',
            'operator' => 'Regel',
            'output' => 'Output',
            'values' => 'Wert',
            'conditions' => 'Konditionen'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
