<?php

namespace App\Environment;

use Ramsey\Uuid\Uuid;

/**
 * Blueprint-Repräsentation einer Aufgabe. Wird für die Environmenterzeugung genutzt.
 * Class Task
 * @package App\Environment
 */
class Task extends AbstractModel {

    public string $id;
    public string $identifier;
    public string $user;

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return Task
     */
    public static function make(array $options = []) {
        $id = $options['id'] ?? Uuid::uuid4()->toString();

        return new self([
            'id' => $id,
            'identifier' => $options['identifier'] ?? '',
            'user' => $options['user'] ?? '',
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'user' => $this->user
        ];
    }
}
