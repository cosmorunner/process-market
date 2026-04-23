<?php

namespace App\ProcessType;

use App\Interfaces\Iconable;
use Ramsey\Uuid\Uuid;

/**
 * Class Listener
 * @package App
 */
class Listener extends AbstractModel implements Iconable {

    public string $id;
    public string $description = '';
    public array $events = [];
    public $relation_type = null;
    public array $conditions = [];
    public string $type = 'execute_action';
    public array $type_options = [];
    public Definition $definition;

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
        return 'hearing';
    }

    /**
     * Erzeugt ein neues Listener-Object mit Standardwerten.
     * @param array $options
     * @return Listener
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()->toString(),
            'description' => trim($options['description'] ?? ''),
            'events' => $options['events'] ?? [],
            'relation_type' => $options['relation_type'] ?? null,
            'conditions' => $options['conditions'] ?? [],
            'type' => $options['type'] ?? '',
            'type_options' => $options['type_options'] ?? []
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'events' => $this->events,
            'relation_type' => $this->relation_type,
            'conditions' => $this->conditions,
            'type' => $this->type,
            'type_options' => $this->type_options
        ];
    }


}

