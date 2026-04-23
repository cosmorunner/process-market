<?php

namespace App\Environment;

/**
 * Blueprint-Repräsentation eines Gruppen-Zugriffs. Wird für die Environmenterzeugung genutzt.
 * Class Process
 * @package App\Environment
 */
class GroupAccess extends AbstractModel {

    public string $group_id;
    public string $user_id;
    public string $role_id;

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return GroupAccess
     */
    public static function make(array $options = []) {
        return new self([
            'group_id' => $options['group_id'] ?? '',
            'user_id' => $options['user_id'] ?? '',
            'role_id' => $options['role_id'] ?? ''
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'group_id' => $this->group_id,
            'user_id' => $this->user_id,
            'role_id' => $this->role_id
        ];
    }
}
