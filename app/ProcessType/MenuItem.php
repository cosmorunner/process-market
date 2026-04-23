<?php

namespace App\ProcessType;

use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Class MenuItem
 * @package App\ProcessType
 */
class MenuItem extends AbstractModel {

    public string $id;
    public string $label;
    public $url;
    public string $location;
    public ?string $route_name;
    public int $sort;
    public string $image;
    public string $target = '_self';
    public ?string $parent_id = null;
    public array $groups = [];
    public array $views = [];
    public array $conditions = [];
    public Definition $definition;

    /**
     * Gibt die Seiten zurück, die der Seite untergeordnet sind.
     */
    public Collection $children;
    public ?MenuItem $parent;

    /**
     * @param Definition $parent
     */
    protected function setup($parent) {
        $this->definition = $parent;
    }

    /**
     * Erzeugt eine neue StatusTyp-Klasse
     * @param array $options
     * @return MenuItem
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()->toString(),
            'label' => $options['label'] ?? '',
            'url' => $options['url'] ?? '',
            'location' => $options['location'] ?? 'LOCATION_SIDEBAR',
            'route_name' => $options['route_name'] ?? null,
            'sort' => $options['sort'] ?? 0,
            'target' => $options['target'] ?? '_self',
            'image' => $options['image'] ?? '',
            'views' => $options['views'] ?? ['layouts.process'],
            'conditions' => $options['conditions'] ?? [],
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'url' => $this->url,
            'location' => $this->location,
            'route_name' => $this->route_name,
            'parent_id' => $this->parent_id,
            'sort' => $this->sort,
            'target' => $this->target,
            'image' => $this->image,
            'views' => $this->views,
            'conditions' => $this->conditions
        ];
    }

}
