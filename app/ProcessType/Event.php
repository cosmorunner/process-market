<?php

namespace App\ProcessType;

use App\Interfaces\Iconable;
use Ramsey\Uuid\Uuid;

/**
 * Class Event
 * @package App
 */
class Event extends AbstractModel implements Iconable {

    public string $id;
    public string $name;
    public string $description;
    public Definition $definition;

    /**
     * Datenfelder, die durch den Prozessor "TriggerEvent" befüllt werden können.
     * @var array
     */
    public array $data = [];

    /**
     * @param Definition $parent
     */
    protected function setup($parent) {
        $this->definition = $parent;
    }

    /**
     * @inheritDoc
     */
    public static function icon(): string {
        return 'flag';
    }

    /**
     * Erzeugt ein neues Event-Object mit Standardwerten.
     * @param array $options
     * @return Event
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()->toString(),
            'name' => trim($options['name'] ?? ''),
            'description' => trim($options['description'] ?? ''),
            'data' => $options['data'] ?? []
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'data' => $this->data
        ];
    }


}

