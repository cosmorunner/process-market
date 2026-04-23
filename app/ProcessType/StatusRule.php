<?php

namespace App\ProcessType;

use Ramsey\Uuid\Uuid;

/**
 * Class StatusRule
 * @package App\ProcessType
 */
class StatusRule extends AbstractModel {

    const OPERATOR_SET = 'SET';
    const OPERATOR_ADD = 'ADD';
    const OPERATOR_SUB = 'SUB';

    public string $id;
    public string $action_type_id;
    public string $status_type_id;
    public string $operator;
    public array $values = [];
    public array $conditions = [];
    public ?string $output = null;
    public ?string $state = null;

    /**
     * Erzeugt ein neues StatusRule-Object mit Standardwerten.
     * @param array $options
     * @return StatusRule
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()->toString(),
            'action_type_id' => $options['action_type_id'] ?? Uuid::uuid4()->toString(),
            'status_type_id' => $options['status_type_id'] ?? Uuid::uuid4()->toString(),
            'operator' => $options['operator'] ?? StatusRule::OPERATOR_SET,
            'output' => $options['output'] ?? '',
            'state' => $options['state'] ?? '',
            'values' => $options['values'] ?? [],
            'conditions' => $options['conditions'] ?? []
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'action_type_id' => $this->action_type_id,
            'status_type_id' => $this->status_type_id,
            'operator' => $this->operator,
            'values' => $this->values,
            'output' => $this->output,
            'state' => $this->state,
            'conditions' => $this->conditions
        ];
    }

}
