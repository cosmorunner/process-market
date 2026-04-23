<?php

namespace App\Environment;

use Ramsey\Uuid\Uuid;

/**
 * Blueprint-Repräsentation eines Connectors. Wird für die Environmenterzeugung genutzt.
 * Class Process
 * @package App\Environment
 */
class Connector extends AbstractModel {

    public string $id;
    public string $name;
    public ?string $description;
    public string $identifier;
    public string $type;
    public string $base_uri;
    public string $mode;
    public bool $active;
    public array $options;

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return Connector
     */
    public static function make(array $options = []) {
        $id = $options['id'] ?? Uuid::uuid4()->toString();

        return new self([
            'id' => $id,
            'name' => $options['name'] ?? 'my_connector',
            'description' => $options['description'] ?? '',
            'identifier' => $options['identifier'] ?? 'my_identifier',
            'type' => $options['type'] ?? 'http',
            'base_uri' => $options['base_uri'] ?? 'https://example.com',
            'mode' => $options['mode'] ?? 'debug',
            'active' => $options['active'] ?? true,
            'options' => $options['options'] ?? [],
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'identifier' => $this->identifier,
            'type' => $this->type,
            'base_uri' => $this->base_uri,
            'mode' => $this->mode,
            'active' => $this->active,
            'options' => $this->options,
        ];
    }
}
