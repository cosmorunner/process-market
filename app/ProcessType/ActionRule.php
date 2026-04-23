<?php

namespace App\ProcessType;

use Ramsey\Uuid\Uuid;

/**
 * Class ActionRule
 * @package App\ProcessType
 */
class ActionRule extends AbstractModel {

    const TYPE_STATUS = 'TYPE_STATUS';

    const OPERATOR_IN_ARRAY = 'IN_ARRAY';
    const OPERATOR_NOT_IN_ARRAY = 'NOT_IN_ARRAY';
    const OPERATOR_LOWER = 'LOWER';
    const OPERATOR_LOWER_OR_EQUAL = 'LOWER_OR_EQUAL';
    const OPERATOR_GREATER = 'GREATER';
    const OPERATOR_GREATER_OR_EQUAL = 'GREATER_OR_EQUAL';
    const OPERATOR_IN_BETWEEN = 'IN_BETWEEN';

    public string $id;
    public string $action_type_id;
    public string $status_type_id;
    public string $type;
    public string $operator;
    public array $values = [];
    public array $state_ids = [];
    public string $group;

    public function __construct(array $properties) {
        parent::__construct($properties);

        $this->values = array_map(fn($val) => as_decimal($val, StatusType::VALUE_PRECISION), $this->values);
    }

    /**
     * Flagge ob sich die Aktionsregel direkt auf Zustände bezieht.
     * @return bool
     */
    public function usesStates() {
        return !empty($this->state_ids) && $this->type === self::TYPE_STATUS;
    }

    /**
     * Flagge ob sich die Aktionsregel auf manuell gesetzte Werte bezieht.
     */
    public function usesValues() {
        return !empty($this->values) && $this->type === self::TYPE_STATUS;
    }

    /**
     * Sortiert Float-Werte aufsteigend.
     * @param $values
     * @return mixed
     */
    public static function sortAscending($values) {
        usort($values, fn($a, $b) => !self::operatorLowerOrEqual($a, $b));

        return $values;
    }

    /**
     * Sortiert Float-Werte absteigend.
     * @param $values
     * @return mixed
     */
    public static function sortDescending($values) {
        usort($values, fn($a, $b) => self::operatorLowerOrEqual($a, $b));

        return $values;
    }

    /**
     * Prüft ob beide Werte identisch sind.
     * @param $value
     * @param $compareValue
     * @return bool
     */
    public static function operatorEqual($value, $compareValue) {
        return abs($value - $compareValue) < PHP_FLOAT_EPSILON;
    }

    /**
     * Prüft ob beide Werte ungleich sind.
     * @param $value
     * @param $compareValue
     * @return bool
     */
    public static function operatorNotEqual($value, $compareValue) {
        return abs($value - $compareValue) > PHP_FLOAT_EPSILON;
    }

    /**
     * Prüft ob der $currentValue kleiner ist als der $compareValue.
     * @param $value
     * @param $compareValue
     * @return bool
     */
    public static function operatorLower($value, $compareValue) {
        return bccomp($value, $compareValue, StatusType::VALUE_PRECISION) === -1;
    }

    /**
     * Prüft ob der $value kleiner oder gleich ist wie der $compareValue.
     * @param $value
     * @param $compareValue
     * @return bool
     */
    public static function operatorLowerOrEqual($value, $compareValue) {
        return in_array(bccomp($value, $compareValue, StatusType::VALUE_PRECISION), [
            0,
            -1
        ]);
    }

    /**
     * Prüft ob der $value größer oder gleich ist wie der $compareValue.
     * @param $value
     * @param $compareValue
     * @return bool
     */
    public static function operatorGreaterOrEqual($value, $compareValue) {
        return in_array(bccomp($value, $compareValue, StatusType::VALUE_PRECISION), [
            0,
            1
        ]);
    }

    /**
     * Prüft ob der $value größer ist als der $compareValue.
     * @param $value
     * @param $compareValue
     * @return bool
     */
    public static function operatorGreater($value, $compareValue) {
        return bccomp($value, $compareValue, StatusType::VALUE_PRECISION) === 1;
    }

    /**
     * Prüft ob der $value in einem der angegebenen Werte ist.
     * @param $value
     * @param $compareValues
     * @return bool
     */
    public static function operatorInArray($value, $compareValues) {
        return in_array($value, $compareValues);
    }

    /**
     * Prüft ob der $value zwischen dem Min/Max-Wertebereich liegt.
     * @param $value
     * @param $min
     * @param $max
     * @return bool
     */
    public static function operatorInBetween($value, $min, $max) {
        return self::operatorGreaterOrEqual($value, $min) && self::operatorLowerOrEqual($value, $max);
    }

    /**
     * Erzeugt ein neues ActionRule-Object mit Standardwerten.
     * @param array $options
     * @return ActionRule
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()->toString(),
            'action_type_id' => $options['action_type_id'] ?? Uuid::uuid4()->toString(),
            'status_type_id' => $options['status_type_id'] ?? Uuid::uuid4()->toString(),
            'type' => ActionRule::TYPE_STATUS,
            'operator' => $options['operator'] ?? ActionRule::OPERATOR_IN_ARRAY,
            'values' => $options['values'] ?? ['1.000'],
            'state_ids' => $options['state_ids'] ?? [],
            'group' => $options['group'] ?? 'group_1'
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'action_type_id' => $this->action_type_id,
            'status_type_id' => $this->status_type_id,
            'type' => $this->type,
            'operator' => $this->operator,
            'values' => array_map(fn($val) => as_decimal($val, StatusType::VALUE_PRECISION), $this->values),
            'state_ids' => $this->state_ids,
            'group' => $this->group
        ];
    }
}
