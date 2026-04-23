<?php

namespace Database\Factories;

use App\Interfaces\Licensable;
use App\Models\License;
use App\Models\Solution;
use App\Models\SolutionLicense;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SolutionLicenseFactory
 * @package Database\Factories
 * @extends Factory<SolutionLicense>
 */
class SolutionLicenseFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = SolutionLicense::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        return [
            'resource_type' => Solution::class,
            'resource_id' => $this->faker->uuid,
            'owner_type' => User::class,
            'owner_id' => $this->faker->uuid,
            'issuer_id' => $this->faker->uuid,
            'level' => License::LEVEL_PRIVATE,
            'level_options' => ['option1' => 'value1', 'option2' => 'value2'],
        ];
    }

    /**
     * Solution license is created with issuer.
     * @param User $issuer
     * @return SolutionLicenseFactory
     */
    public function withIssuer($issuer = null) {
        if ($issuer === null) {
            $issuer = User::factory()->create();
        }

        return $this->afterCreating(function (SolutionLicense $solutionLicense) use ($issuer) {
            $solutionLicense->issuer()->associate($issuer)->save();
        });
    }

    /**
     * Solution license is created with owner.
     * @param Model $owner
     * @return SolutionLicenseFactory
     */
    public function withOwner($owner = null) {
        if ($owner === null) {
            $owner = User::factory()->create();
        }

        return $this->afterCreating(function (SolutionLicense $solutionLicense) use ($owner) {
            $solutionLicense->owner()->associate($owner)->save();
        });
    }

    /**
     * Solution license is created with resource.
     * @param Model|Eloquent|Licensable|Solution $resource
     * @return SolutionLicenseFactory
     */
    public function withResource($resource = null) {
        if ($resource === null) {
            $resource = Solution::factory()->create();
        }

        return $this->afterCreating(function (SolutionLicense $solutionLicense) use ($resource) {
            $solutionLicense->resource()->associate($resource)->save();
        });
    }
}
