<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SettingFactory
 * @package Database\Factories
 */
class SettingFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        return [
            'name' => $this->faker->word,
            'value' => $this->faker->word,
            'owner_id' => $this->faker->uuid,
            'owner_type' => User::class
        ];
    }

    /**
     * A setting is created with an organisation as the owner.
     * @param Organisation|null $owner
     * @return SettingFactory
     */
    public function ofOrganisationOwner(Organisation $owner = null) {
        return $this->afterCreating(function (Setting $setting) use ($owner) {
            $owner = $owner ?? Organisation::factory()->create();
            $setting->owner()->associate($owner)->save();
        });
    }

    /**
     * A setting is created with a user as the owner.
     * @param Model|null $owner
     * @return SettingFactory
     */
    public function ofUser(Model $owner = null) {
        return $this->afterCreating(function (Setting $setting) use ($owner) {
            $owner = $owner ?? User::factory()->create();
            $setting->owner()->associate($owner)->save();
        });
    }

    /**
     * A setting is created with the system as the owner.
     * @return SettingFactory
     */
    public function ofSystem() {
        return $this->state(function () {
            return [
                'owner_id' => null,
                'owner_type' => null,
            ];
        });
    }
}