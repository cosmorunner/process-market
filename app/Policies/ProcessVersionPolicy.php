<?php /** @noinspection PhpUnused */

namespace App\Policies;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class GraphPolicy
 * @package App\Policies
 */
class ProcessVersionPolicy {

    use HandlesAuthorization;

    /**
     * Check if user can view the process version.
     * @return false
     * @noinspection PhpUnused
     */
    public function view(User $user, ProcessVersion $processVersion) {
        if (!$processVersion->process instanceof Process) {
            return false;
        }

        return $user->can('view', $processVersion->process);
    }

    /**
     * Check if user can update a process version.
     * @param User $user
     * @param ProcessVersion $processVersion
     * @return bool
     */
    public function update(User $user, ProcessVersion $processVersion) {
        if (!$processVersion->process instanceof Process) {
            return false;
        }

        return !$processVersion->isPublished() && $user->can('update', $processVersion->process) && !$processVersion->process->isArchived();
    }

    /**
     * Check if the user can complete a new version of the process version.
     * @param User $user
     * @param ProcessVersion $processVersion
     * @return bool
     */
    public function complete(User $user, ProcessVersion $processVersion) {
        if (!$processVersion->process instanceof Process) {
            return false;
        }

        // The user must be able to edit the process and no simulation must be active.
        return !$processVersion->isPublished() && $user->can('complete', $processVersion->process);
    }

    /**
     * Check if the user can restore the process version (reset to earlier development).
     * @param User $user
     * @param ProcessVersion $processVersion
     * @return bool
     */
    public function restore(User $user, ProcessVersion $processVersion) {
        if (!$processVersion->process instanceof Process) {
            return false;
        }

        // The version must be published
        return !$processVersion->process->isArchived() && ($processVersion->isPublished() && $user->can('complete', $processVersion->process));
    }

    /**
     * Check if user can download a process version.
     * @param User $user
     * @param ProcessVersion $processVersion
     * @return bool
     */
    public function download(User $user, ProcessVersion $processVersion) {
        // User can always download its own processes.
        if ($processVersion->process->authoredByUser($user)) {
            return true;
        }

        // If process is created by an organization...
        if ($processVersion->process->authoredByOrganisation()) {
            // ... user needs this permission and user must be an organisation member
            return $user->hasOrganisationPermission($processVersion->process->author, 'process_versions.export');
        }

        return false;
    }

    /**
     * Check if the user can sync the process version to an Allisa Plattform.
     * @param User $user
     * @param ProcessVersion $processVersion
     * @return bool
     */
    public function sync(User $user, ProcessVersion $processVersion) {
        if (!$processVersion->process instanceof Process) {
            return false;
        }

        return $user->can('sync', $processVersion->process);
    }

    /**
     * Check if the user can create an environment for the process version.
     * @param User $user
     * @param ProcessVersion $processVersion
     * @return bool
     * @noinspection PhpUnused
     */
    public function createEnvironment(User $user, ProcessVersion $processVersion) {
        if (!$processVersion->process instanceof Process) {
            return false;
        }

        // The process version must not be published yet and the user must be able to update the process.
        return !$processVersion->isPublished() && $user->can('update', $processVersion->process);
    }

}
