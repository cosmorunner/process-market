<?php

namespace App\Policies;

use App\Models\Process;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ProcessPolicy
 * @package App\Policies
 */
class ProcessPolicy {

    use HandlesAuthorization;

    /**
     * Check whether the user or anonymous user is allowed to start a demo of the process.
     * @param User $user
     * @param Process $process
     * @return bool
     * @noinspection PhpUnused
     */
    public function start_demo(User $user, Process $process): bool {
        return $user->authored($process) || $user->roleWithin($process->author)?->can('demos.view');
    }

    /**
     * Check whether the user is allowed to view the process.
     * @param User $user
     * @param Process $process
     * @return bool
     */
    public function view(User $user, Process $process): bool {
        return $user->authored($process) || $user->roleWithin($process->author)?->can('processes.view');
    }

    /**
     * Check whether the user is allowed to purchase a process license.
     * @param User $user
     * @param Process $process
     * @return bool
     */
    public function purchase(User $user, Process $process): bool {
        return $user->authored($process) || $user->roleWithin($process->author)?->can('licenses.manage');
    }

    /**
     * Check whether the user is allowed to see the "edit" view of a process.
     * @param User $user
     * @param Process $process
     * @return bool
     */
    public function edit(User $user, Process $process): bool {
        return $user->authored($process) || $user->roleWithin($process->author)?->can('processes.update');
    }

    /**
     * Check whether the user is allowed to update the process via PATCH request.
     * @param User $user
     * @param Process $process
     * @return bool
     */
    public function update(User $user, Process $process): bool {
        return !$process->isArchived() && ($user->authored($process) || $user->roleWithin($process->author)
                    ?->can('processes.update'));
    }

    /**
     * Check whether the user is allowed to export a process version to an Allisa platform.
     * @param User $user
     * @param Process $process
     * @return bool
     */
    public function sync(User $user, Process $process): bool {
        return $user->authored($process) || $user->roleWithin($process->author)?->can('process_versions.export');
    }

    /**
     * Check whether the user is allowed to complete a process version.
     * @param User $user
     * @param Process $process
     * @return bool
     */
    public function complete(User $user, Process $process): bool {
        return !$process->isArchived() && ($user->authored($process) || $user->roleWithin($process->author)
                    ?->can('process_versions.create'));
    }

    /**
     * Determine whether the user can delete the process.
     * @param User $user
     * @param Process $process
     * @return bool
     */
    public function delete(User $user, Process $process): bool {
        return $user->authored($process) || $user->roleWithin($process->author)?->can('processes.delete');
    }

    /**
     * Check whether the user is allowed to update the process.
     * @param User $user
     * @param Process $process
     * @return bool
     */
    public function restore(User $user, Process $process): bool {
        return $process->isArchived() && ($user->authored($process) || $user->roleWithin($process->author)
                    ?->can('processes.update'));
    }
}
