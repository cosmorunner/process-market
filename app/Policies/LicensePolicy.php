<?php

namespace App\Policies;

use App\Models\License;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class LicensePolicy
 * @package App\Policies
 */
class LicensePolicy {

    use HandlesAuthorization;

    /**
     * Anlegen einer Lizenz.
     * @param User $user
     * @return bool
     */
    public function create(User $user) {
        $receiver = request('receiver');
        $receiver = User::whereNamespace($receiver)->first() ?? Organisation::whereNamespace($receiver)->first();

        if ($receiver instanceof Organisation) {
            return $user->roleWithin($receiver)?->can('licenses.manage');
        }

        return true;
    }

    /**
     * Man kann die Lizenz einsehen, wenn einem selbst die Lizenz gehört oder die Rolle in der Organisation dies erlaubt.
     * @param User $user
     * @param License $license
     * @return bool
     */
    public function view(User $user, License $license) {
        return $license->ownedByUser($user) || ($license->ownedByOrganisation() && $user->roleWithin($license->owner)?->can('licenses.view'));
    }

    /**
     * Prüfung ob der Benutzer von der Lizenz eine Demo starten darf.
     * @param User $user
     * @param License $license
     * @return bool
     * @noinspection PhpUnused
     */
    public function start_demo(User $user, License $license) {
        return $license->ownedByUser($user) || $user->roleWithin($license->owner)->can('demos.view');
    }

    /**
     * Prüfung, ob der Benutzer die Resource zu einer Allisa Plattform exportieren darf.
     * @param User $user
     * @param License $license
     * @return bool
     */
    public function sync(User $user, License $license) {
        // Falls die Lizenz direkt dem Benutzer gehört, darf dieser
        if ($license->ownedByUser($user)) {
            return true;
        }

        // Wenn die Lizenz einer Organisation gehört, wird je nach Prozess oder Lösung unterschieden.
        if ($license->ownedByOrganisation()) {
            if ($license->resource instanceof Process) {
                return $user->roleWithin($license->owner)?->can('process_versions.export');
            }

            if ($license->resource instanceof Solution) {
                return $user->roleWithin($license->owner)?->can('solution_versions.export');
            }
        }

        return false;
    }

}
