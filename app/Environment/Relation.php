<?php

namespace App\Environment;

use Ramsey\Uuid\Uuid;

/**
 * Blueprint-Repräsentation eines Prozesses. Wird für die Environmenterzeugung genutzt.
 * Class Process
 * @package App\Environment
 */
class Relation extends AbstractModel {

    public string $id;
    public string $left;
    public string $relation_type;
    public ?string $relation_type_name = '';
    public string $right;
    public array $data = [];

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return Relation
     */
    public static function make(array $options = []) {
        $id = $options['id'] ?? Uuid::uuid4()->toString();

        return new self([
            'id' => $id,
            'left' => $options['left'] ?? '',
            'relation_type' => $options['relation_type'] ?? '',
            'relation_type_name' => $options['relation_type_name'] ?? '',
            'right' => $options['right'] ?? '',
            'data' => $options['data'] ?? []
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'left' => $this->left,
            'relation_type' => $this->relation_type,
            'relation_type_name' => $this->relation_type_name,
            'right' => $this->right,
            'data' => $this->data
        ];
    }
}
