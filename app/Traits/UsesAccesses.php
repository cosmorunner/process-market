<?php

namespace App\Traits;

use App\Models\Access;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Die Entität erhält die Möglichkeit, Benutzern oder Organisation Zugriff auf sie zu gewähren und zu entziehen.
 * Siehe App\Interfaces\Accessible.
 * Class UsesAccessible
 * @package App\Traits
 */
trait UsesAccesses {

    /**
     * Zugriffe von Benutzern auf Ressource.
     * @return MorphMany
     */
    public function accesses(): MorphMany {
        return $this->morphMany(Access::class, 'resource');
    }

    /**
     * Fügt einen Zugriff für einen Empfänger mit einer Rolle der Resource hinzu.
     * @param Model|User $recipient
     * @param Model|Role|\App\ProcessType\Role $role
     * @return Access
     */
    public function addAccess($recipient, $role): Access {
        return Access::grant($this, $recipient, $role);
    }

    /**
     * Updates the access of a user to a resource.
     * Existing accesses are removed.
     * @param Model|User $recipient
     * @param Model|Role|\App\ProcessType\Role $role
     * @return Access
     */
    public function updateAccess($recipient, $role): Access {
        $existingAccess = $this->accessesByUser($recipient)->first();
        $this->removeRecipient($recipient);

        $access = Access::grant($this, $recipient, $role);

        if ($existingAccess instanceof Access) {
            $access->update(['created_at' => $existingAccess->created_at]);
        }

        return $access;
    }

    /**
     * Adds a user with a specific role to the resource.
     * @param User $user
     * @param Role $role
     * @return Access
     */
    public function addUser(User $user, $role): Access {
        return $this->addAccess($user, $role);
    }

    /**
     * Removes a user from the resource.
     * @param User $user
     * @return void
     */
    public function removeUser(User $user): void {
        $this->removeRecipient($user);
    }

    /**
     * Returns the accesses of the user within the resource.
     * @param User $user
     * @return Collection
     */
    public function accessesByUser(User $user): Collection {
        return $this->accesses()->where('recipient_id', '=', $user->id)->where('recipient_type', '=', User::class)->get();
    }

    /**
     * Removes access to the resource from a recipient.
     * @param Model $recipient
     */
    public function removeRecipient(Model $recipient): void {
        $this->accesses->firstWhere('recipient_id', '=', $recipient->id)->delete();
    }

    /**
     * Removes all access to the organisation.
     */
    public function removeAllAccesses() {
        $this->accesses->each(fn(Access $access) => $access->delete());
    }
}
