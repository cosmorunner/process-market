<?php

namespace App\Interfaces;

use App\Models\Access;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Die Entität erhält die Möglichkeit, Benutzer des Systems Zugriff auf sie zu gewähren und zu entziehen.
 * @package App\Interfaces
 */
interface Accessible {

    /**
     * Alle Zugriffe auf die Entität.
     * @return MorphMany
     */
    public function accesses() : MorphMany;

    /**
     * Gewährt einem Benutzer Zugriff auf die Entität/Resource.
     * @param User $user
     * @param Role $role
     * @return Access
     */
    public function addUser(User $user, Role $role) : Access;

    /**
     * Entzieht einem Benutzer Zugriff auf die Entität/Resource.
     * @param Model $recipient
     */
    public function removeRecipient(Model $recipient) : void;

    /**
     * Gewährt einem Empänger Zugriff auf die Entität/Resource.
     * @param $recipient
     * @param Role $role
     * @return Access
     */
    public function addAccess($recipient, Role $role) : Access;

}
