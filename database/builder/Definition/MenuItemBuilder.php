<?php

namespace Database\Builder\Definition;

use App\ProcessType\MenuItem;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class MenuItemBuilder
 * @package Database\Builder
 */
class MenuItemBuilder extends AbstractBuilder {

    public function definition(): array {
        return [
            'id' => Uuid::uuid4()->toString(),
            'label' => 'Menu Item 1',
            'url' => 'https://example.com',
            'location' => 'LOCATION_SIDEBAR',
            'route_name' => null,
            'sort' => 0,
            'target' => '_self',
            'image' => '',
            'views' => ['layouts.process'],
            'conditions' => [],
        ];
    }

    /**
     * @param array $attributes
     * @return MenuItem
     */
    public function make(array $attributes = []) {
        return new MenuItem(array_merge($this->state, $attributes));
    }
}
