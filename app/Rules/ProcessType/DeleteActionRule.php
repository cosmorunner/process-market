<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\Definition;
use App\ProcessType\StatusType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class DeleteActionRule
 * @package App\Rules\ProcessType
 */
class DeleteActionRule implements ValidationRule {

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
            $fail('Ungültige Command-Eingabedaten.');
        }

        $validator = Validator::make((array) $value, [
            'status_type_id' => ['bail', 'required', 'string', 'uuid', Rule::in($this->statusTypeIds)],
            'action_type_id' => ['bail', 'required', 'string', 'uuid', Rule::in($this->actionTypeIds)],
        ], [], [
            'action_type_id' => 'Aktion',
            'status_type_id' => 'Status',
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
