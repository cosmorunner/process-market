<?php

namespace App\Policies;

use App\Models\Organisation;
use App\Models\System;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SystemPolicy
 * @package App\Policies
 */
class SystemPolicy {

    use HandlesAuthorization;

    /**
     * Löschen eines Allisa Systems.
     * @param User $user
     * @param System $system
     * @return bool
     */
    public function delete(User $user, System $system) {
        if ($system->owner_id === $user->id) {
            return true;
        }

        return $system->owner instanceof Organisation && $system->owner->roleOf($user)?->can('platforms.manage');
    }

}
