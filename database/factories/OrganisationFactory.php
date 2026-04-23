<?php

namespace Database\Factories;

use App\Models\Access;
use App\Models\Invitation;
use App\Models\License;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\ProcessLicense;
use App\Models\Role;
use App\Models\SolutionLicense;
use App\Models\System;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Jdenticon\Identicon;
use Ramsey\Uuid\Uuid;

/**
 * Class OrganisationFactory
 * @package Database\Factories
 */
class OrganisationFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Organisation::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        $id = Uuid::uuid4()->toString();
        $icon = new Identicon();
        $icon->setValue($id);
        $icon->setSize(250);

        return [
            'name' => $this->faker->word,
            'namespace' => strtolower($this->faker->unique()->word) . '-test',
            'description' => $this->faker->words(5, true),
            'image' => $icon->getImageDataUri('svg'),
            'city' => $this->faker->city,
            'link' => ''
        ];
    }

    /**
     * Organization is created with access.
     * @param array|Collection|Access|null $accesses
     * @return OrganisationFactory
     */
    public function withAccesses(array|Collection|Access $accesses = null) {
        if ($accesses === null) {
            $accesses = [Access::factory()->create(), Access::factory()->create()];
        }
        else if ($accesses instanceof Access) {
            $accesses = [$accesses];
        }
        else {
            foreach ($accesses as $access) {
                if (!$access instanceof Access) {
                    throw new InvalidArgumentException('All elements in the $accesses ' . (is_array($accesses) ? 'array' : $accesses::class) . ' must be instances of ' . Access::class);
                }
            }
        }

        return $this->afterCreating(function (Organisation $organisation) use ($accesses) {
            $organisation->accesses()->saveMany($accesses);
        });
    }

    /**
     * Organization is created with invitation.
     * @param array|Collection|Invitation|null $invitations
     * @return OrganisationFactory
     */
    public function withInvitations(array|Collection|Invitation $invitations = null) {
        if ($invitations === null) {
            $invitations = [Invitation::factory()->create(), Invitation::factory()->create()];
        }
        else if ($invitations instanceof Invitation) {
            $invitations = [$invitations];
        }
        else {
            foreach ($invitations as $invitation) {
                if (!$invitation instanceof Invitation) {
                    throw new InvalidArgumentException('All elements in the $invitations ' . (is_array($invitations) ? 'array' : $invitations::class) . ' must be instances of ' . Invitation::class);
                }
            }
        }

        return $this->afterCreating(function (Organisation $organisation) use ($invitations) {
            $organisation->invitations()->saveMany($invitations);
        });
    }

    /**
     * Organization is created with process.
     * @param array|Collection|Process|null $processes
     * @return OrganisationFactory
     */
    public function withProcesses(array|Collection|Process $processes = null) {
        if ($processes === null) {
            $processes = [Process::factory()->create(), Process::factory()->create()];
        }
        else if ($processes instanceof Process) {
            $processes = [$processes];
        }
        else {
            foreach ($processes as $process) {
                if (!$process instanceof Process) {
                    throw new InvalidArgumentException('All elements in the $processes ' . (is_array($processes) ? 'array' : $processes::class) . ' must be instances of ' . Process::class);
                }
            }
        }

        return $this->afterCreating(function (Organisation $organisation) use ($processes) {
            $organisation->processes()->saveMany($processes);
        });
    }

    /**
     * Organization is created with role.
     * @param array|Collection|Role|null $roles
     * @return OrganisationFactory
     */
    public function withRoles(array|Collection|Role $roles = null) {
        if ($roles === null) {
            $roles = [Role::factory()->create(), Role::factory()->create()];
        }
        else if ($roles instanceof Role) {
            $roles = [$roles];
        }
        else {
            foreach ($roles as $role) {
                if (!$role instanceof Role) {
                    throw new InvalidArgumentException('All elements in the $roles ' . (is_array($roles) ? 'array' : $roles::class) . ' must be instances of ' . Role::class);
                }
            }
        }

        return $this->afterCreating(function (Organisation $organisation) use ($roles) {
            $organisation->roles()->saveMany($roles);
        });
    }

    /**
     * Organization is created with system.
     * @param array|Collection|System|null $systems
     * @return OrganisationFactory
     */
    public function withSystems(array|Collection|System $systems = null) {
        if ($systems === null) {
            $systems = [System::factory()->create(), System::factory()->create()];
        }
        else if ($systems instanceof System) {
            $systems = [$systems];
        }
        else {
            foreach ($systems as $system) {
                if (!$system instanceof System) {
                    throw new InvalidArgumentException('All elements in the $systems ' . (is_array($systems) ? 'array' : $systems::class) . ' must be instances of ' . System::class);
                }
            }
        }

        return $this->afterCreating(function (Organisation $organisation) use ($systems) {
            $organisation->systems()->saveMany($systems);
        });
    }

    /**
     * Organization is created with license.
     * @param array|Collection|License|null $licenses
     * @return OrganisationFactory
     */
    public function withLicenses(array|Collection|License $licenses = null) {
        if ($licenses === null) {
            $licenses = [License::factory()->create(), License::factory()->create()];
        }
        else if ($licenses instanceof License) {
            $licenses = [$licenses];
        }
        else {
            foreach ($licenses as $license) {
                if (!$license instanceof License) {
                    throw new InvalidArgumentException('All elements in the $licenses ' . (is_array($licenses) ? 'array' : $licenses::class) . ' must be instances of ' . License::class);
                }
            }
        }

        return $this->afterCreating(function (Organisation $organisation) use ($licenses) {
            $organisation->licenses()->saveMany($licenses);
        });
    }

    /**
     * Organization is created with process-license.
     * @param array|Collection|ProcessLicense|null $processLicenses
     * @return OrganisationFactory
     */
    public function withProcessLicenses(array|Collection|ProcessLicense $processLicenses = null) {
        if ($processLicenses === null) {
            $processLicenses = ProcessLicense::factory()->withResource()->count(2)->create();
        }
        else if ($processLicenses instanceof ProcessLicense) {
            $processLicenses = [$processLicenses];
        }
        else {
            foreach ($processLicenses as $processLicense) {
                if (!$processLicense instanceof ProcessLicense) {
                    throw new InvalidArgumentException('All elements in the $processLicenses ' . (is_array($processLicenses) ? 'array' : $processLicenses::class) . ' must be instances of ' . ProcessLicense::class);
                }
            }
        }

        return $this->afterCreating(function (Organisation $organisation) use ($processLicenses) {
            $organisation->processLicenses()->saveMany($processLicenses);
        });
    }

    /**
     * Organization is created with solution-license.
     * @param array|Collection|SolutionLicense|null $solutionLicenses
     * @return OrganisationFactory
     */
    public function withSolutionLicenses(array|Collection|SolutionLicense $solutionLicenses = null) {
        if ($solutionLicenses === null) {
            $solutionLicenses = SolutionLicense::factory()->withResource()->count(2)->create();
        }
        else if ($solutionLicenses instanceof SolutionLicense) {
            $solutionLicenses = [$solutionLicenses];
        }
        else {
            foreach ($solutionLicenses as $solutionLicense) {
                if (!$solutionLicense instanceof SolutionLicense) {
                    throw new InvalidArgumentException('All elements in the $solutionLicenses ' . (is_array($solutionLicenses) ? 'array' : $solutionLicenses::class) . ' must be instances of ' . SolutionLicense::class);
                }
            }
        }

        return $this->afterCreating(function (Organisation $organisation) use ($solutionLicenses) {
            $organisation->solutionLicenses()->saveMany($solutionLicenses);
        });
    }


    /**
     * Adds default roles.
     * @return OrganisationFactory
     */
    public function withDefaultRoles() {
        return $this->afterCreating(function (Organisation $organisation) {
            $ownerRole = Role::factory()->ofOwnerType()->create();
            $adminRole = Role::factory()->ofAdministratorType()->create();
            $managerRole = Role::factory()->ofManagerType()->create();
            $developerRole = Role::factory()->ofDeveloperType()->create();
            $reporterRole = Role::factory()->ofReporterType()->create();

            $organisation->roles()->saveMany(collect([$ownerRole, $adminRole, $managerRole, $developerRole, $reporterRole]));
        });
    }

    /**
     * Adds an owner to the group.
     * @param User $user
     * @return OrganisationFactory
     */
    public function withOwner(User $user) {
        return $this->afterCreating(function (Organisation $organisation) use ($user) {
            $organisation->addUser($user, $organisation->ownerRole());
        });
    }

    /**
     * Adds an administrator to the group.
     * @param User $user
     * @return OrganisationFactory
     */
    public function withAdministrator(User $user) {
        return $this->afterCreating(function (Organisation $organisation) use ($user) {
            $organisation->addUser($user, $organisation->adminRole());
        });
    }
}
