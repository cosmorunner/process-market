<?php

namespace Database\Factories;

use App\Interfaces\Accessible;
use App\Models\Access;
use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

/**
 * Class RoleFactory
 * @package Database\Factories
 */
class RoleFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'owner_id' => Uuid::uuid4()->toString(),
            'owner_type' => Organisation::class,
            'is_admin' => false,
            'is_owner' => false
        ];
    }

    /**
     * Sets the role as owner with rights.
     * @return RoleFactory
     */
    public function ofOwnerType() {
        $factory = $this->state([
            'name' => 'Eigentümer',
            'description' => 'Vollständigen Zugriff auf alle Bereiche der Organisation sowie die Verwaltung der Administratoren.',
            'is_admin' => true,
            'is_owner' => true
        ]);

        return $factory->withOwnerPermissions();
    }

    /**
     * Sets the role as administrator with rights.
     * @return RoleFactory
     */
    public function ofAdministratorType() {
        $factory = $this->state([
            'name' => 'Administrator',
            'description' => 'Vollständigen Zugriff auf alle Bereiche der Organisation.',
            'is_admin' => true
        ]);

        return $factory->withAdministratorPermissions();
    }

    /**
     * Sets the role as manager with rights.
     * @return RoleFactory
     */
    public function ofManagerType() {
        $factory = $this->state([
            'name' => 'Manager',
            'description' => 'Prozesse, Lösungen, Plattformen und Lizenzen verwalten.',
            'is_admin' => false
        ]);

        return $factory->withManagerPermissions();
    }

    /**
     * Sets the role as developer with rights.
     * @return RoleFactory
     */
    public function ofDeveloperType() {
        $factory = $this->state([
            'name' => 'Prozess-Entwickler',
            'description' => 'Prozesse & Lösungen entwickeln & exportieren.',
            'is_admin' => false
        ]);

        return $factory->withDeveloperPermissions();
    }

    /**
     * Sets the role as reporter with rights.
     * @return RoleFactory
     */
    public function ofReporterType() {
        $factory = $this->state([
            'name' => 'Reporter',
            'description' => 'Prozesse & Lösungen einsehen und testen.',
            'is_admin' => false
        ]);

        return $factory->withReporterPermissions();
    }

    /**
     * Adds the administrator permissions to the role.
     * @return RoleFactory
     */
    public function withOwnerPermissions() {
        return $this->afterCreating(function (Role $role) {
            $permissions = Permission::whereIn('ident', config('roles.owner.permissions', []))->get();

            $role->permissions()->saveMany($permissions);
        });
    }

    /**
     * Adds the administrator permissions to the role.
     * @return RoleFactory
     */
    public function withAdministratorPermissions() {
        return $this->afterCreating(function (Role $role) {
            $permissions = Permission::whereIn('ident', config('roles.admin.permissions', []))->get();

            $role->permissions()->saveMany($permissions);
        });
    }

    /**
     * Adds the manager permissions to the role.
     * @return RoleFactory
     */
    public function withManagerPermissions() {
        return $this->afterCreating(function (Role $role) {
            $permissions = Permission::whereIn('ident', config('roles.manager.permissions', []))->get();

            $role->permissions()->saveMany($permissions);
        });
    }

    /**
     * Adds the developer permissions to the role.
     * @return RoleFactory
     */
    public function withDeveloperPermissions() {
        return $this->afterCreating(function (Role $role) {
            $permissions = Permission::whereIn('ident', config('roles.developer.permissions', []))->get();

            $role->permissions()->saveMany($permissions);
        });
    }

    /**
     * Adds the reporter permissions to the role.
     * @return RoleFactory
     */
    public function withReporterPermissions() {
        return $this->afterCreating(function (Role $role) {
            $permissions = Permission::whereIn('ident', config('roles.reporter.permissions', []))->get();

            $role->permissions()->saveMany($permissions);
        });
    }

    /**
     * Locks the role.
     * @param Bool $locked
     * @return RoleFactory
     */
    public function isLocked($locked = true) {
        return $this->state([
            'locked' => $locked
        ]);
    }

    /**
     * Creates an owner to which the role belongs.
     * @param Model|Accessible|null $owner
     * @return RoleFactory
     */
    public function withOwner(Model|Accessible $owner = null) {
        if ($owner === null) {
            $owner = Organisation::factory()->create();
        }

        return $this->afterCreating(function (Role $role) use ($owner) {
            $role->owner()->associate($owner);
        });
    }

    /**
     * Role is created with accesses.
     * @param array|Collection|Access|null $accesses
     * @return RoleFactory
     */
    public function withAccesses(array|Collection|Access $accesses = null) {
        return $this->afterCreating(function (Role $role) use ($accesses) {
            if ($accesses === null) {
                $accesses = Access::factory()->count(2)->create();
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

            $role->accesses()->saveMany($accesses);
        });
    }

    /**
     * Role is created with permissions.
     * @param array|Collection|Permission|null $permissions
     * @return RoleFactory
     */
    public function withPermissions(array|Collection|Permission $permissions = null) {
        return $this->afterCreating(function (Role $role) use ($permissions) {
            if ($permissions === null) {
                $permissions = Permission::factory()->count(2)->ofRole([$role])->create();
            }
            else if ($permissions instanceof Permission) {
                $permissions = [$permissions];
            }
            else {
                foreach ($permissions as $permission) {
                    if (!$permission instanceof Permission) {
                        throw new InvalidArgumentException('All elements in the $permissions ' . (is_array($permissions) ? 'array' : $permissions::class) . ' must be instances of ' . Permission::class);
                    }
                }
            }

            $role->permissions()->saveMany($permissions);
        });
    }

}