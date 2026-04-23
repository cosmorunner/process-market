<?php

namespace App\ProcessType;

use Ramsey\Uuid\Uuid;

/**
 * Class RelationType
 * @package App\ProcessType
 */
class RelationType extends AbstractModel {

    public string $id;
    public string $name;
    public string $description;
    public ?string $reference = null;
    public Definition $definition;

    /**
     * The available values are: '1-1','1-n','n-1','n-n'
     * @var string
     */
    public string $connection_type;

    /**
     * Standard-Daten der Verknüpfung.
     * @var array
     */
    public array $default = [];

    /**
     * @param Definition $parent
     */
    protected function setup($parent) {
        $this->definition = $parent;
    }

    /**
     * Erzeugt ein neues RelationType-Object mit Standardwerten.
     * @param array $options
     * @return RelationType
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()->toString(),
            'name' => trim($options['name'] ?? 'Neuer Verknüpfungstyp'),
            'description' => trim($options['description'] ?? ''),
            'connection_type' => $options['connection_type'] ?? 'n-n',
            'reference' => $options['reference'] ?? null,
            'default' => $options['default'] ?? []
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'connection_type' => $this->connection_type,
            'reference' => $this->reference,
            'default' => $this->default
        ];
    }
}

