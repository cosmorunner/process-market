<?php

namespace Database\Factories;

use App\Environment\Group;
use App\Models\Access;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AccessFactory
 * @package Database\Factories
 */
class AccessFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Access::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        return [
            'role_id' => $this->faker->uuid,
            'resource_id' => $this->faker->uuid,
            'resource_type' => Organisation::class,
            'recipient_id' => $this->faker->uuid,
            'recipient_type' => User::class
        ];
    }

    /**
     * Recipient who receives access
     * @param User|Group|null $recipient
     * @return AccessFactory
     */
    public function ofRecipient(User|Group $recipient = null) {
        if ($recipient === null) {
            $recipient = User::factory()->create();
        }

        return $this->afterCreating(function(Access $access) use ($recipient) {
            $access->update(
                [
                    'recipient_id' => $recipient->id,
                    'recipient_type' => $recipient::class,
                ]
            );

            $access->recipient()->associate($recipient)->save();
        });
    }

    /**
     * Resource to which access is granted.
     * @param Process|Group|null $resource
     * @return AccessFactory
     */
    public function ofResource(Process|Group $resource = null) {
        if ($resource === null) {
            $resource = Process::factory()->create();
        }

        return $this->afterCreating(function (Access $access) use ($resource) {
            $access->resource()->associate($resource)->save();
        });
    }

    /**
     * Role to which access is granted.
     * @param Role|null $role
     * @return AccessFactory
     */
    public function ofRole(Role $role = null) {
        if ($role === null) {
            $role = Role::factory()->create();
        }

        return $this->afterCreating(function (Access $access) use ($role) {
            $access->role()->associate($role)->save();
        });
    }
}
