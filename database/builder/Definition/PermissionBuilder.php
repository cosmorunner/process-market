<?php

namespace Database\Builder\Definition;

use App\ProcessType\Permission;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class PermissionBuilder
 * @package Database\Builder\Definition
 */
class PermissionBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'name' => 'Prozess-Recht',
            'description' => '',
            'ident' => 'ident',
            'scope' => 'App\ProcessType',
            'conditions' => []
        ];
    }

    /**
     * @param array $attributes
     * @return Permission
     */
    public function make(array $attributes = []) {
        return new Permission(array_merge($this->state, $attributes));
    }

    /**
     * @param string $name
     * @return $this
     */
    public function withName(string $name) {
        return $this->state([
            'name' => $name
        ]);
    }

    /**
     * @param string $ident
     * @return $this
     */
    public function withIdent(string $ident) {
        return $this->state([
            'ident' => $ident
        ]);
    }
}
