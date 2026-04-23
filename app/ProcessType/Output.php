<?php

namespace App\ProcessType;

use App\Models\Process;
use Illuminate\Support\Arr;

/**
 * Class Output
 * @package App\ProcessType
 */
class Output extends AbstractModel {

    /**
     * Der Output ist ein String, Integer, Boolean oder NULL.
     */
    const TYPE_BASIC = 'basic';

    /**
     * Der Output ist ein JSON-Array.
     */
    const TYPE_ARRAY = 'array';

    /**
     * Der Output ist ein JSON-Objekt.
     */
    const TYPE_OBJECT = 'object';

    public string $name;
    public ?string $description;
    public null|string|array $default;
    public ?Process $process;
    public ?ActionType $actionType;
    public array $validation = [];
    public string $type = self::TYPE_BASIC;
    public array $type_options = [];
    public ?Definition $definition;

    /**
     * Erzeugt ein neues Output-Object mit Standardwerten.
     * @param array $options
     * @return Output
     */
    public static function make(array $options = []) {
        return new self([
            'name' => strtolower($options['name'] ?? 'output_' . rand(1, 1000)),
            'description' => (string)($options['description'] ?? ''),
            // Here, ‘_empty_’ is sent along with an empty ‘default’ string, as Laravel's automatic ‘’ -> null transformation
            // otherwise no explicit ‘null’ can be provided.
            'default' => ($options['default'] ?? null) === '_empty_' ? '' : $options['default'] ?? null,
            'type' => $options['type'] ?? self::TYPE_BASIC,
            'type_options' => $options['type_options'] ?? [],
            'validation' => $options['validation'] ?? [],
        ]);
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'default' => $this->default,
            'type' => $this->type,
            'type_options' => $this->type_options,
            'validation' => $this->validation,
        ];
    }

    /**
     * Prüft der Output eine bestimmte Validierungsregel hat.
     * @param string $name
     * @return bool
     */
    public function hasValidationRule(string $name): bool {
        foreach ($this->validation as $rule) {
            if (str_starts_with($rule, $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gibt eine Validierungsregel zurück.
     * @param string $name
     * @return string|null
     */
    public function getValidationRule(string $name) {
        if (!$this->hasValidationRule($name)) {
            return null;
        }

        foreach ($this->validation as $rule) {
            if (str_starts_with($rule, $name)) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Flagge ob der Output die "file" Validierungsregel hat.
     * @return bool
     */
    public function hasFileValidation() {
        return in_array('file', $this->validation);
    }

    /**
     * Flagge ob der Output einfache Daten hält.
     * @return boolean
     */
    public function isBasicType() {
        return $this->type === self::TYPE_BASIC;
    }

    /**
     * Flagge ob der Output ein JSON Array ist.
     * @return boolean
     */
    public function isArrayType() {
        return $this->type === self::TYPE_ARRAY;
    }

    /**
     * Flagge ob der Output ein JSON Objekt ist.
     * @return boolean
     */
    public function isObjectType() {
        return $this->type === self::TYPE_OBJECT;
    }

    /**
     * Identifiziert anhand eines Wertes den Input-Typ.
     * @param $val
     * @return string
     */
    public static function identifyType($val) {
        if (is_array($val) && !Arr::isAssoc($val)) {
            return self::TYPE_ARRAY;
        }
        if (is_array($val) && Arr::isAssoc($val)) {
            return self::TYPE_OBJECT;
        }

        return self::TYPE_BASIC;
    }

}

