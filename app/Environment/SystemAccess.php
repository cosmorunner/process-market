<?php

namespace App\Environment;

/**
 * Blueprint-Repräsentation eines System-Zugriffs. Wird für die Environmenterzeugung genutzt.
 * Class Process
 * @package App\Environment
 */
class SystemAccess extends AbstractModel {

    public string $user_id;
    public string $role_id;

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return SystemAccess
     */
    public static function make(array $options = []) {
        return new self([
            'user_id' => $options['user_id'] ?? '',
            'role_id' => $options['role_id'] ?? ''
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'user_id' => $this->user_id,
            'role_id' => $this->role_id
        ];
    }
}
