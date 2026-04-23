<?php

namespace Database\Factories;

use App\Models\Solution;
use App\Models\SolutionVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SolutionVersion>
 */
class SolutionVersionFactory extends Factory {

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition() {
        return [
            'solution_id' => $this->faker->uuid,
            'data' => [],
            'version' => '0.0.1',
            'changelog' => '',
            'published_at' => null
        ];
    }

    /**
     * Sets the solution of the version.
     * @param null $solution
     * @param bool $setAsLatest Sets the solution version as the latest (most current) version of the solution.
     * @return SolutionVersionFactory
     */
    public function ofSolution($solution = null, $setAsLatest = false): SolutionVersionFactory {
        return $this->afterCreating(function (SolutionVersion $solutionVersion) use ($solution, $setAsLatest) {
            $solution = $solution ?? Solution::factory()->create();
            $solutionVersion->solution()->associate($solution)->save();

            if ($setAsLatest) {
                $solution->update(['latest_version_id' => $solutionVersion->id]);
            }

            if ($solutionVersion->isPublished()) {
                $solution->update(['latest_published_version_id' => $solutionVersion->id]);
            }
        });
    }

    /**
     * Sets the version.
     * @param string $version
     * @return SolutionVersionFactory
     */
    public function ofVersion(string $version) {
        return $this->state(function () use ($version) {
            return [
                'version' => $version
            ];
        });
    }
}
