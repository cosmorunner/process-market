<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InvitationFactory
 * @package Database\Factories
 */
class InvitationFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invitation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'email' => $this->faker->email,
            'resource_id' => $this->faker->uuid,
            'resource_type' => Organisation::class,
            'role_id' => $this->faker->uuid,
            'creator_id' => $this->faker->uuid,
            'expires_at' => Carbon::now()->addWeek(),
        ];
    }

    /**
     * Sets the “expires_at” timestamp to the past.
     * @return InvitationFactory
     */
    public function expired() {
        return $this->state(function () {
            return [
                'expires_at' => Carbon::now()->subWeek(),
            ];
        });
    }

    /**
     * Formats the invitation to a system invitation by removing the resource.
     * @return InvitationFactory
     */
    public function system() {
        return $this->state(function () {
            return [
                'role_id' => null,
                'resource_id' => null,
                'resource_type' => null,
            ];
        });
    }

    /**
     * Invitation is generated with a resource.
     * @param  Model|null $resource
     * @return InvitationFactory
     */
    public function ofResource(Model $resource = null) {
        if ($resource === null){
            $resource = Organisation::factory()->create();
        }

        return $this->afterCreating(function(Invitation $invitation) use ($resource) {
            $invitation->resource()->associate($resource)->save();
        });
    }

    /**
     * Invitation is generated with a role.
     * @param Role|null $role
     * @return InvitationFactory
     */
    public function ofRole(Role $role = null) {
        if ($role === null){
            $role = Role::factory()->create();
        }

        return $this->afterCreating(function(Invitation $invitation) use ($role) {
            $invitation->role()->associate($role)->save();
        });
    }

    /**
     * Invitation is generated with a creator.
     * @param User|null $creator
     * @return InvitationFactory
     */
    public function ofCreator(User $creator = null) {
        if ($creator === null){
            $creator = User::factory()->create();
        }

        return $this->afterCreating(function(Invitation $invitation) use ($creator) {
            $invitation->creator()->save($creator);
        });
    }
}
