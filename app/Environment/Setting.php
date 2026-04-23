<?php

namespace App\Environment;

/**
 * Blueprint-Repräsentation einer System-Einstellung. Wird für die Environmenterzeugung genutzt.
 * Class Process
 * @package App\Environment
 */
class Setting extends AbstractModel {

    public string $name;
    public mixed $value;
    public ?string $owner_id;
    public ?string $owner_type;

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return Setting
     */
    public static function make(array $options = []) {
        return new self([
            'name' => $options['name'] ?? '',
            'value' => $options['value'] ?? '',
            'owner_id' => $options['owner_id'] ?? null,
            'owner_type' => $options['owner_type'] ?? null
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'owner_id' => $this->owner_id,
            'owner_type' => $this->owner_type
        ];
    }
}
