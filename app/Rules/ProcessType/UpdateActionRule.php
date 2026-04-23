<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\ActionRule;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\StatusType;
use App\Rules\ValidStateIds;
use App\Rules\ValidStatusValues;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateActionRule
 * @package App\Rules\ProcessType
 */
class UpdateActionRule implements ValidationRule {

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
        ActionRule::OPERATOR_IN_ARRAY,
        ActionRule::OPERATOR_NOT_IN_ARRAY,
        ActionRule::OPERATOR_IN_BETWEEN,
        ActionRule::OPERATOR_LOWER,
        ActionRule::OPERATOR_LOWER_OR_EQUAL,
        ActionRule::OPERATOR_GREATER,
        ActionRule::OPERATOR_GREATER_OR_EQUAL
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

        // Prüfen ob die Aktion bereits eine Aktionsregel für den Status hat.
        if (!$this->actionType->actionRule($this->statusTypeId) instanceof ActionRule) {
            $fail(['action_type_id' => ['Regel existiert nicht.']]);
        }

        $stateIds = $this->statusType->states->pluck('id')->toArray();

        $validator = Validator::make((array) $value, [
            'status_type_id' => ['bail', 'required', 'string', 'uuid', Rule::in($this->statusTypeIds)],
            'action_type_id' => ['bail', 'required', 'string', 'uuid', Rule::in($this->actionTypeIds)],
            'operator' => ['bail', 'required', 'string', Rule::in($this->operators)],
            'values' => ['array', 'required_without:state_ids', new ValidStatusValues],
            'state_ids' => ['array', 'required_without:values', new ValidStateIds($stateIds)],
        ], [], [
            'action_type_id' => 'Aktion',
            'status_type_id' => 'Status',
            'operator' => 'Regel',
            'values' => 'Werte',
            'state_ids' => 'Zustände'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
