<?php

namespace App\ProcessType;

use Illuminate\Support\Arr;

/**
 * Class Input
 * @package App\ProcessType
 */
class Input extends AbstractModel {

    /**
     * Der Input ist ein String.
     */
    const TYPE_BASIC = 'basic';

    /**
     * Der Input ist ein JSON-Array. String wird zu einem leeren Array.
     */
    const TYPE_ARRAY = 'array';

    /**
     * Der Input ist ein JSON-Objekt. String wird zu einem leeren Objekt.
     */
    const TYPE_OBJECT = 'object';

    /**
     * Der Input ist der Inhalt einer Liste.
     */
    const TYPE_LIST_CONFIG = 'list_config';

    /**
     * Der zu ladene Wert entscheidet, Wert wird nicht gecastet..
     */
    const TYPE_AUTO = 'auto';

    public string $name;
    public null|string|array $value;
    public string $type = self::TYPE_AUTO;
    public array $type_options = [];


    /**
     * Erzeugt ein neues Input-Object mit Standardwerten.
     * @param array $options
     * @return Input
     */
    public static function make(array $options = []) {
        return new self([
            'name' => $options['name'] ?? ('input_' . rand(1, 1000)),
            'value' => $options['value'] ?? '',
            'type' => $options['type'] ?? self::TYPE_BASIC,
            'type_options' => $options['type_options'] ?? [],
        ]);
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'type' => $this->type,
            'type_options' => $this->type_options,
        ];
    }

    /**
     * Flagge ob der Input einfache Daten hält.
     * @return boolean
     */
    public function isBasicType() {
        return $this->type === self::TYPE_BASIC;
    }

    /**
     * Flagge ob der Input ein JSON Array ist.
     * @return boolean
     */
    public function isArrayType() {
        return $this->type === self::TYPE_ARRAY;
    }

    /**
     * Flagge ob der Input ein JSON Objekt ist.
     * @return boolean
     */
    public function isObjectType() {
        return $this->type === self::TYPE_OBJECT;
    }

    /**
     * Flagge ob der Input der Inhalt einer Liste ist.
     * @return bool
     */
    public function isListConfigType() {
        return $this->type === self::TYPE_LIST_CONFIG;
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

