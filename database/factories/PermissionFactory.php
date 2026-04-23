<?php

namespace Database\Factories;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

/**
 * Class PermissionFactory
 * @package Database\Factories
 */
class PermissionFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        return [
            'id' => Uuid::uuid4()->toString(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentences(2, true),
            'ident' => $this->faker->words(3, true),
            'active' => true
        ];
    }

    /**
     * Links the permission to a role.
     * @param array|Collection|Role|null $roles
     * @return PermissionFactory
     */
    public function ofRole(array|Collection|Role $roles = null) {
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

        return $this->afterCreating(function (Permission $permission) use ($roles) {
            $permission->roles()->saveMany($roles);
        });
    }

    /**
     * Deactivates the right.
     * @return PermissionFactory
     */
    public function deactivated() {
        return $this->state([
            'active' => false
        ]);
    }

    /**
     * Activates the right.
     * @return PermissionFactory
     */
    public function activated() {
        return $this->state([
            'active' => true
        ]);
    }
}
