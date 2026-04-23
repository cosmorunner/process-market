<?php

namespace Database\Builder\Definition;

use App\ProcessType\Category;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class CategoryBuilder
 * @package Database\Builder\Definition
 */
class CategoryBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'name' => 'Workflow',
            'description' => '',
            'image' => 'settings',
            'sort' => 1,
            'color' => '#aad0ff',
            'locked' => false,
            'hidden' => false
        ];
    }

    /**
     * @param array $attributes
     * @return Category
     */
    public function make(array $attributes = []) {
        return new Category(array_merge($this->state, $attributes));
    }

    /**
     * Non editable category
     * @return CategoryBuilder
     */
    public function ofSystemType() {
        return $this->state([
            'locked' => true
        ]);
    }
}
