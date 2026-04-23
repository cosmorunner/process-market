<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\Synchronization;
use App\Models\System;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SystemFactory
 * @package Database\Factories
 */
class SystemFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = System::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'id' => $this->faker->uuid,
            'name' => $this->faker->word,
            'url' => $this->faker->url,
            'owner_id' => $this->faker->uuid,
            'owner_type' => User::class,
            'client_id' => $this->faker->uuid,
            'client_secret' => 'MIX3H8HTce40cwEHNhJiSfIMZXovDKMmnQjkSCcl',
            'token' => $this->faker->text(200),
            'expires_at' => Carbon::now()->addYear()
        ];
    }

    /**
     * Setzt den Eigentümer des Systems.
     * @param Model|User|Organisation $owner
     * @return SystemFactory
     */
    public function ofOwner(Model $owner) {
        return $this->state(function () use ($owner) {
            return [
                'owner_id' => $owner->id,
                'owner_type' => get_class($owner)
            ];
        });
    }

    /**
     * Fügt eine Synchronisation zum System hinzu.
     * @return SystemFactory
     */
    public function withSynchronization() {
        return $this->afterCreating(function (System $system) {
            $system->synchronizations()->save(Synchronization::factory()->create());
        });
    }
}
