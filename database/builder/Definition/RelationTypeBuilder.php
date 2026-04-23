<?php

namespace Database\Builder\Definition;

use App\ProcessType\RelationType;
use Database\Builder\AbstractBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Class RelationTypeBuilder
 * @package Database\Builder\Definition
 */
class RelationTypeBuilder extends AbstractBuilder {

    /**
     * @return array
     */
    public function definition(): array {
        return [
            'id' => Uuid::uuid4(),
            'name' => 'relation type name',
            'description' => '',
            'connection_type' => 'n-n',
            'reference' => 'ref',
            'default' => []
        ];
    }

    /**
     * @param array $attributes
     * @return RelationType
     */
    public function make(array $attributes = []) {
        return new RelationType(array_merge($this->state, $attributes));
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
     * @param string $reference
     * @return $this
     */
    public function withReference(string $reference) {
        return $this->state([
            'reference' => $reference
        ]);
    }

    /**
     * @param string $connectionType
     * @return $this
     */
    public function withConnectionType(string $connectionType) {
        return $this->state([
            'connection_type' => $connectionType
        ]);
    }

}
