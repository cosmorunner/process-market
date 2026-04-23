<?php

namespace Database\Factories;

use App\Interfaces\Licensable;
use App\Models\License;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\Solution;
use App\Models\Synchronization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 *
 */
class LicenseFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = License::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        return [
            'resource_type' => User::class,
            'resource_id' => $this->faker->uuid,
            'owner_type' => Organisation::class,
            'owner_id' => $this->faker->uuid,
            'issuer_id' => $this->faker->uuid,
            'level' => 'open-source',
            'level_options' => ['test'],
        ];
    }

    /**
     * Creates license with a resource.
     * @param Licensable|Process|Solution|null $resource
     * @return LicenseFactory
     */
    public function withResource(Licensable|Process|Solution $resource = null) {
        if ($resource === null) {
            $resource = Process::factory()->create();
        }

        return $this->afterCreating(function (License $license) use ($resource) {
            $license->resource()->associate($resource)->save();
        });
    }

    /**
     * Creates license with an owner.
     * @param User|Organisation|null $owner
     * @return LicenseFactory
     */
    public function withOwner(User|Organisation $owner = null) {
        if ($owner === null) {
            $owner = User::factory()->create();
        }

        return $this->afterCreating(function (License $license) use ($owner) {
            $license->update([
                'owner_id' => $owner->id,
                'owner_type' => $owner::class
            ]);

            $license->owner()->associate($owner)->save();
        });
    }

    /**
     * Creates license with synchronizations.
     * @param array|Collection|Synchronization|null $synchronizations
     * @return LicenseFactory
     */
    public function withSynchronization(array|Collection|Synchronization $synchronizations = null) {
        if ($synchronizations === null) {
            $synchronizations = [Synchronization::factory()->create()];
        }
        else if ($synchronizations instanceof Synchronization) {
            $synchronizations = collect($synchronizations);
        }
        else {
            foreach ($synchronizations as $synchronization) {
                if (!$synchronization instanceof Synchronization) {
                    throw new InvalidArgumentException('All elements in the $synchronizations ' . (is_array($synchronizations) ? 'array' : $synchronizations::class) . ' must be instances of ' . Synchronization::class);
                }
            }
        }

        return $this->afterCreating(function (License $license) use ($synchronizations) {
            $license->synchronizations()->saveMany($synchronizations);
        });
    }

    /**
     * Creates license with an owner.
     * @param User|null $issuer
     * @return LicenseFactory
     */
    public function withIssuer(User $issuer = null) {
        if ($issuer === null) {
            $issuer = User::factory()->create();
        }

        return $this->afterCreating(function (License $license) use ($issuer) {
            $license->issuer()->associate($issuer)->save();
        });
    }

    /**
     * Sets the level of the License.
     * @param string $level
     * @return LicenseFactory
     */
    public function withLevel(string $level = License::LEVEL_OPEN_SOURCE) {
        return $this->state(fn() => ['level' => $level]);
    }
}
