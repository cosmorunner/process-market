<?php

namespace App\Interfaces;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Die Entität besitzt und verwaltet Rollen, mit denen Benutzern Zugriff auf die Entität gewährt werden kann.
 * Interface Roles
 * @package App\Interfaces
 */
interface HandlesRoles {

    /**
     * Rollen, die die Entität besitzt.
     * @return MorphMany
     */
    public function roles();

    /**
     * Fügt eine Rolle zur Entität hinzu.
     * @param Role $role
     */
    public function addRole(Role $role) : void;

    /**
     * Entfernt eine Rolle von der Entität.
     * @param Role $role
     */
    public function removeRole(Role $role) : void;

}
