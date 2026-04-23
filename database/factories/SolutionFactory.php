<?php

namespace Database\Factories;

use App\Enums\Visibility;
use App\Models\Solution;
use App\Models\SolutionVersion;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<Solution>
 */
class SolutionFactory extends Factory {

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition() {
        $uuid = Uuid::uuid4()->toString();
        $namespace = 'ns-' . strtolower(Str::random(4));
        $identifier = 'id-' . strtolower(Str::random(4));

        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence,
            'namespace' => $namespace,
            'identifier' => $identifier,
            'creator_id' => $uuid,
            'author_type' => User::class,
            'author_id' => $uuid,
            'visibility' => Visibility::Public->value,
            'latest_version' => '0.0.1',
            'latest_version_id' => $this->faker->uuid,
            'latest_published_version_id' => $this->faker->uuid,
            'full_namespace' => $namespace . '/' . $identifier,
            'license_options' => [
                [
                    'level' => 'mixed',
                    'level_options' => []
                ]
            ]
        ];
    }

    /**
     * Setzt die Sichtbarkeit der Solution.
     * @param int $visivility
     * @return $this
     */
    public function withVisibility(int $visivility) {
        return $this->state(function () use ($visivility) {
            return [
                'visibility' => $visivility
            ];
        });
    }

    /**
     * Process is created with process version.
     * @param Collection|SolutionVersion[] $versions
     * @return SolutionFactory
     */
    public function withSolutionVersion($versions = []) {
        if (empty($versions)) {
            $versions = [SolutionVersion::factory()->create()];
        }
        else if ($versions instanceof SolutionVersion) {
            $versions = [$versions];
        }
        else {
            foreach ($versions as $version) {
                if (!$version instanceof SolutionVersion) {
                    throw new InvalidArgumentException('All elements in the $versions ' . (is_array($versions) ? 'array' : $versions::class) . ' must be instances of ' . SolutionVersion::class);
                }
            }
        }

        return $this->afterCreating(function (Solution $solution) use ($versions) {
            $solution->versions()->saveMany($versions);
        });
    }

    /**
     * Solution is created with a last solution version.
     * @param SolutionVersion|null $version
     * @return SolutionFactory
     */
    public function withLatestVersion(SolutionVersion $version = null) {
        return $this->afterCreating(function (Solution $solution) use ($version) {
            $version = $version ?? SolutionVersion::factory()->ofSolution($solution)->create();
            $solution->versions()->save($version);
            $solution->latestVersion()->associate($version);
        });
    }

    /**
     * Sets the latest published version id to null.
     * @return SolutionFactory
     */
    public function withoutLatestPublishedVersion() {
        return $this->state(function () {
            return [
                'latest_published_version_id' => null
            ];
        });
    }

    /**
     * Solution is created with a latest published solution version.
     * @param SolutionVersion|null $version
     * @return SolutionFactory
     */
    public function withLatestPublishedSolutionVersion(SolutionVersion $version = null) {
        return $this->afterCreating(function (Solution $solution) use ($version) {
            $version = $version ?? SolutionVersion::factory()->create();
            $solution->versions()->save($version);
            $solution->latestPublishedVersion()->associate($version);
            $solution->update(['latest_published_version_id' => $version->id]);
        });
    }

    /**
     * Sets the creator and author of solution
     * @param User $user
     * @return $this
     */
    public function ofCreatorAndAuthor(User $user) {
        return $this->state(function () use ($user) {
            return [
                'creator_id' => $user->id,
                'namespace' => $user->namespace,
                'author_type' => $user::class,
                'author_id' => $user->id,
            ];
        })->afterCreating(function (Solution $solution) use ($user) {
            $solution->creator()->associate($user)->save();
            $solution->author()->associate($user)->save();
            $solution->update(['full_namespace' => $user->namespace . '/' . $solution->identifier]);
        });
    }

    /**
     * Fügt Tags zur Solution hinzu.
     * @param array $tags
     * @return $this
     */
    public function withTags(array $tags = []) {
        if (empty($tags)) {
            $tags = ['foo', 'bar'];
        }

        return $this->afterCreating(function (Solution $solution) use ($tags) {
            $solution->tags()->saveMany(collect($tags)->map(fn($tag) => Tag::factory()->name($tag)->create()));
        });
    }
}
