<?php

namespace App\Policies;

use App\Models\ProcessVersion;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SimulationPolicy
 * @package App\Policies
 */
class SimulationPolicy {

    use HandlesAuthorization;

    /**
     * Starting a simulation.
     * @param User $user
     * @param ProcessVersion $processVersion
     * @return bool
     */
    public function create(User $user, ProcessVersion $processVersion): bool {
        return $user->authored($processVersion->process)
            || $user->roleWithin($processVersion->process->author)?->can('demos.view')
            || $processVersion->process->isPubliclyAccessible()
            || !$processVersion->process->hasNoLicense();
    }

    /**
     * Display a running simulation. Either the simulation was started by a logged-in user
     * in which case only the logged-in user can see the simulation, or the simulation was started by a guest and the underlying process is public.
     * started by a guest and the underlying process is public.
     * @param User $user
     * @param Simulation $simulation
     * @return bool
     */
    public function view(User $user, Simulation $simulation): bool {
        return $user->id === $simulation->user_id || ($simulation->organisation && $user->hasAccessTo($simulation->organisation));
    }

    /**
     * Check whether the user is allowed to run or end the simulation.
     * @param User $user
     * @param Simulation|null $simulation
     * @return bool
     */
    public function update(User $user, Simulation $simulation = null): bool {
        return $user->id === $simulation->user_id || ($simulation->organisation && $user->hasAccessTo($simulation->organisation));
    }

    /**
     * End/delete a running simulation.
     * @param User $user
     * @param Simulation $simulation
     * @return bool
     */
    public function delete(User $user, Simulation $simulation): bool {
        return $user->id === $simulation->user_id || ($simulation->organisation && $user->hasAccessTo($simulation->organisation));
    }
}
