<?php

namespace App\ProcessType;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Class StatusType
 * @package App\ProcessType
 */
class StatusType extends AbstractModel {

    const VALUE_PRECISION = 3;
    const VALUE_MAX_LENGTH = 13;

    const SMART_RELATION_TYPE = 'relation_type';

    public string $id;
    public string $reference;
    public string $name;
    public ?string $description;
    public string $namespace;
    public string $identifier;
    public string $version;
    public array $options = [];
    public array $smart = [];
    public ?string $image;
    public int $sort;
    public string $size;
    public string $default;
    public bool $hidden = false;
    public Collection $states;
    public Definition $definition;

    protected $collections = [
        'states' => State::class
    ];

    /**
     * @param Definition $parent
     */
    protected function setup($parent) {
        $this->definition = $parent;
    }

    /**
     * Erzeugt eine neue StatusTyp-Klasse
     * @param array $options
     * @return StatusType
     */
    public static function make(array $options = []) {
        try {
            $default = State::validateValue($options['default'] ?? '0.000');
        }
        catch (Exception) {
            $default = '0.000';
        }
        $id = array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()->toString();
        $name = $options['name'] ?? 'Neuer Status';
        $randomReference = Str::limit(Str::slug($name, '_'), 16, "") . '_' . strtolower(Str::random(4));

        return new self([
            'id' => $id,
            'reference' => empty($options['reference']) ? $randomReference : $options['reference'],
            'name' => $options['name'] ?? 'Neuer Status',
            'description' => $options['description'] ?? '',
            'namespace' => $options['namespace'] ?? 'allisa',
            'identifier' => $options['identifier'] ?? 'simple',
            'version' => $options['version'] ?? '1.0.0',
            'options' => $options['options'] ?? [],
            'smart' => $options['smart'] ?? [],
            'image' => $options['image'] ?? '',
            'sort' => $options['sort'] ?? 0,
            'size' => $options['size'] ?? '6x1',
            'hidden' => (bool) ($options['hidden'] ?? false),
            'default' => $default,
            'states' => $options['states'] ?? []
        ]);
    }

    /**
     * Gibt eine Zustand anhand einer Id zurück.
     * @param string $stateId
     * @return State
     */
    public function state(string $stateId) {
        return $this->states->firstWhere('id', '=', $stateId);
    }

    /**
     * Gibt den Typ des Smart-Status zurück.
     * @return string
     */
    public function smartType() {
        return $this->smart['type'] ?? null;
    }

    /**
     * Flagge ob der Statustyp ein Smart-Status ist.
     * @param string|null $type Optionale Übergabe eines bestimmten "Smart-Status"-Typs.
     */
    public function isSmartStatus(string $type = null) {
        if (!$type) {
            return !empty($this->smart);
        }

        return !empty($this->smart) && ($this->smartType() === $type);
    }

    /**
     * Gibt die Optionen eines Smart-Status zurück.
     * @return array
     */
    public function smartOptions(): array {
        return $this->smart['options'] ?? [];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'name' => $this->name,
            'description' => $this->description,
            'namespace' => $this->namespace,
            'options' => $this->options,
            'smart' => $this->smart,
            'identifier' => $this->identifier,
            'version' => $this->version,
            'image' => $this->image,
            'sort' => $this->sort,
            'size' => $this->size,
            'default' => $this->default,
            'hidden' => $this->hidden,
            'states' => $this->states->map(fn(State $state) => $state->toArray())->toArray()
        ];
    }

    /**
     * Gibt den ganzen Namespace mit Version des Statustyps zurück.
     * @return string
     */
    public function getFullNamespace() {
        return $this->namespace . '/' . $this->identifier . '@' . $this->version;
    }
}
