<?php

namespace App\Environment;

use Ramsey\Uuid\Uuid;

/**
 * Blueprint-Repräsentation eines Prozesses. Wird für die Environmenterzeugung genutzt.
 * Class Process
 * @package App\Environment
 */
class Process extends AbstractModel {

    public string $id;
    public string $process_type;
    public string $name;
    public ?string $description;
    public ?string $image;
    public ?string $tags;
    public ?string $reference;
    public array $initial_data;
    public array $initial_situation;
    public array $accesses;


    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return Process
     */
    public static function make(array $options = []) {
        $id = $options['id'] ?? Uuid::uuid4()->toString();

        return new self([
            'id' => $id,
            'process_type' => $options['process_type'] ?? '',
            'name' => $options['name'] ?? 'Prozess ' . substr($id, 0, 4),
            'description' => $options['description'] ?? '',
            'image' => 'star',
            'tags' => $options['tags'] ?? '',
            'reference' => $options['reference'] ?? '',
            'initial_data' => $options['initial_data'] ?? [],
            'initial_situation' => $options['initial_situation'] ?? [],
            'accesses' => $options['accesses'] ?? [],
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'process_type' => $this->process_type,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'tags' => $this->tags,
            'reference' => $this->reference,
            'initial_data' => $this->initial_data,
            'initial_situation' => $this->initial_situation,
            'accesses' => $this->accesses,
        ];
    }
}
