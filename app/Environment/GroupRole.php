<?php

namespace App\Environment;

use Illuminate\Support\Str;

/**
 * Blueprint-Repräsentation einer Gruppen-Role. Wird für die Environmenterzeugung genutzt.
 * Class Process
 * @package App\Environment
 */
class GroupRole extends AbstractModel {

    public string $id;
    public string $group_id;
    public string $name;
    public bool $locked;

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return GroupRole
     */
    public static function make(array $options = []) {
        return new self([
            'id' => $options['id'] ?? Str::uuid()->toString(),
            'group_id' => $options['group_id'] ?? Str::uuid()->toString(),
            'name' => $options['name'] ?? 'Demo',
            'locked' => $options['locked'] ?? true,
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'name' => $this->name,
            'locked' => $this->locked
        ];
    }
}
