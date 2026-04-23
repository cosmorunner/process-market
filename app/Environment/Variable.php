<?php

namespace App\Environment;

use Ramsey\Uuid\Uuid;

/**
 * Blueprint-Repräsentation einer Variable. Wird für die Environmenterzeugung genutzt.
 * Class Variable
 * @package App\Environment
 */
class Variable extends AbstractModel {

    public string $id;
    public string $identifier;
    public string $type;
    public null|string|array $value = '';
    public bool $is_public = false;

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return Variable
     */
    public static function make(array $options = []) {
        $id = $options['id'] ?? Uuid::uuid4()->toString();
        $value = array_key_exists('value', $options) && is_null($options['value']) ? '' : $options['value'];

        return new self([
            'id' => $id,
            'identifier' => $options['identifier'] ?? '',
            'type' => $options['type'] ?? 'TYPE_STRING',
            'value' => $value,
            'is_public' => $options['is_public'] ?? false
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'type' => $this->type,
            'value' => $this->value,
            'is_public' => $this->is_public
        ];
    }
}
