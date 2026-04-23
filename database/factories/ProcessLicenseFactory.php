<?php

namespace Database\Factories;

use App\Interfaces\Licensable;
use App\Models\License;
use App\Models\Process;
use App\Models\ProcessLicense;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProcessLicenseFactory
 * @package Database\Factories
 */
class ProcessLicenseFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = ProcessLicense::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        return [
            'resource_type' => Process::class,
            'resource_id' => $this->faker->uuid,
            'owner_type' => User::class,
            'owner_id' => $this->faker->uuid,
            'issuer_id' => $this->faker->uuid,
            'level' => License::LEVEL_PRIVATE,
            'level_options' => ['allow_copy' => null, 'option2' => 'value2'],
        ];
    }

    /**
     * Process license is created with issuer.
     * @param User $issuer
     * @return ProcessLicenseFactory
     */
    public function withIssuer($issuer = null) {
        if ($issuer === null) {
            $issuer = User::factory()->create();
        }

        return $this->afterCreating(function (ProcessLicense $processLicense) use ($issuer) {
            $processLicense->issuer()->associate($issuer)->save();
        });
    }

    /**
     * Process license is created with owner.
     * @param Model $owner
     * @return ProcessLicenseFactory
     */
    public function withOwner($owner = null) {
        if ($owner === null) {
            $owner = User::factory()->create();
        }

        return $this->afterCreating(function (ProcessLicense $processLicense) use ($owner) {
            $processLicense->owner()->associate($owner)->save();
        });
    }

    /**
     * Process license is created with resource.
     * @param Model|Eloquent|Licensable|Process $resource
     * @return ProcessLicenseFactory
     */
    public function withResource($resource = null) {
        if ($resource === null) {
            $resource = Process::factory()->create();
        }

        return $this->afterCreating(function (ProcessLicense $processLicense) use ($resource) {
            $processLicense->resource()->associate($resource)->save();
        });
    }
}
