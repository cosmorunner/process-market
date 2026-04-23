<?php

namespace Database\Factories;

use App\Models\Demo;
use App\Models\License;
use App\Models\Organisation;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Demo>
 */
class DemoFactory extends Factory {

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'user_id' => $this->faker->uuid,
            'solution_id' => $this->faker->uuid,
            'organisation_id' => $this->faker->uuid,
            'solution_version_id' => $this->faker->uuid,
            'token' => $this->faker->windowsPlatformToken,
            'allisa_user_id' => $this->faker->uuid,
        ];
    }

    /**
     * Demo is created with admin mode.
     * @return DemoFactory
     */
    public function withAdminMode() {
        return $this->state(fn () => ['main' => true]);
    }

    /**
     * Demo is created with user.
     * @param User|null $user
     * @return DemoFactory
     */
    public function withUser(User $user = null) {
        if ($user === null){
            $user = User::factory()->create();
        }

        return $this->afterCreating(function(Demo $demo) use ($user) {
            $demo->user()->associate($user)->save();
        });
    }

    /**
     * Demo is created with Solution.
     * @param Solution|null $solution
     * @return DemoFactory
     */
    public function withSolution(Solution $solution = null) {
        if ($solution === null){
            $solution = Solution::factory()->create();
        }

        return $this->afterCreating(function(Demo $demo) use ($solution) {
            $demo->solution()->associate($solution)->save();
        });
    }

    /**
     * Demo is created with organization.
     * @param Organisation|null $organisation
     * @return DemoFactory
     */
    public function withOrganisation(Organisation $organisation = null) {
        if ($organisation === null){
            $organisation = Organisation::factory()->create();
        }

        return $this->afterCreating(function(Demo $demo) use ($organisation) {
            $demo->organisation()->associate($organisation)->save();
        });
    }

    /**
     * Demo is created with license.
     * @param License|null $license
     * @return DemoFactory
     */
    public function withLicense(License $license = null) {
        if ($license === null){
            $license = License::factory()->create();
        }

        return $this->afterCreating(function(Demo $demo) use ($license) {
            $demo->license()->associate($license)->save();
        });
    }
}
