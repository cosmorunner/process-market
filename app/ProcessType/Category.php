<?php

namespace App\ProcessType;

use Ramsey\Uuid\Uuid;

/**
 * Class Category
 * @package App\ProcessType
 */
class Category extends AbstractModel {

    public string $id;
    public string $name;
    public string $description;
    public string $image;
    public string $color;
    public int $sort;
    public bool $locked = false;
    public bool $hidden = false;
    public Definition $definition;

    /**
     * @param Definition $parent
     */
    protected function setup($parent) {
        $this->definition = $parent;
    }

    /**
     * Erzeugt ein neues Category-Object mit Standardwerten.
     * @param array $options
     * @return Category
     */
    public static function make(array $options = []) {
        return new self([
            'id' => array_key_exists('id', $options) && is_string($options['id']) && Uuid::isValid($options['id']) ? $options['id'] : Uuid::uuid4()->toString(),
            'name' => $options['name'] ?? 'Category ' . rand(1, 1000),
            'description' => (string)($options['description'] ?? ''),
            'image' => $options['image'] ?? 'settings',
            'color' => $options['color'] ?? '#72c6ff',
            'sort' => $options['sort'] ?? 0,
            'locked' => $options['locked'] ?? false,
            'hidden' => $options['hidden'] ?? false
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'color' => $this->color,
            'sort' => $this->sort,
            'locked' => $this->locked,
            'hidden' => $this->hidden
        ];
    }
}
