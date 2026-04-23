<?php

namespace App\Traits;

use App\Models\Role;
use Exception;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait UsesRoles
 * @package App\Traits
 */
trait UsesRoles {

    /**
     * Rollen die die Entität besitzt.
     * @return MorphMany
     */
    public function roles() : MorphMany {
        return $this->morphMany(Role::class, 'owner');
    }

    /**
     * Fügt eine Rolle zur Entität hinzu.
     * @param Role $role
     */
    public function addRole(Role $role) : void {
        $this->roles()->save($role);
    }

    /**
     * Entfernt eine Rolle von der Entität.
     * Accesses auf die Entität mit dieser Rolle werden entfernt.
     * @param Role $role
     */
    public function removeRole(Role $role) : void {
        try {
            $role = Role::findOrFail($role->id);
            $role->delete();
        } catch (Exception) {
            // Ignorieren, da model nicht mehr vorhanden ist.
        }
    }
}
