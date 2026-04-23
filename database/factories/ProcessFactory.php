<?php

namespace Database\Factories;

use App\Enums\Visibility;
use App\Models\License;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Simulation;
use App\Models\Tag;
use App\Models\User;
use App\ProcessType\Definition;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

/**
 * Class ProcessFactory
 * @package Database\Factories
 */
class ProcessFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Process::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        $uuid = Uuid::uuid4()->toString();
        $namespace = $this->faker->word;
        $identifier = $this->faker->word . '-test';

        return [
            'title' => $this->faker->words(3, true),
            'creator_id' => $uuid,
            'description' => $this->faker->sentence,
            'namespace' => $namespace,
            'identifier' => $identifier,
            'visibility' => Visibility::Private->value,
            'author_id' => $uuid,
            'author_type' => User::class,
            'template_id' => $this->faker->uuid,
            'license_options' => [
                [
                    "level" => "open-source",
                    "level_options" => []
                ]
            ],
            'latest_version' => 'develop',
            'latest_version_id' => $this->faker->uuid,
            'latest_published_version_id' => $this->faker->uuid,
            'full_namespace' => $namespace . '/' . $identifier
        ];
    }

    /**
     * Process is created with the creator.
     * @param User|null $creator
     * @return ProcessFactory
     */
    public function ofCreator(User $creator = null) {
        if ($creator === null) {
            $creator = User::factory()->create();
        }

        return $this->afterCreating(function (Process $process) use ($creator) {
            $process->creator()->associate($creator)->save();
        });
    }

    /**
     * Process is created with the author.
     * @param Model|Eloquent|Organisation|User $author
     * @return ProcessFactory
     */
    public function ofAuthor($author = null) {
        if ($author === null) {
            $author = User::factory()->create();
        }

        return $this->afterCreating(function (Process $process) use ($author) {
            $process->author()->associate($author)->save();
        });
    }

    /**
     * Sets the creator and author of the process.
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
        })->afterCreating(function (Process $process) use ($user) {
            $process->creator()->associate($user)->save();
            $process->author()->associate($user)->save();
            $process->update(['full_namespace' => $user->namespace . '/' . $process->identifier]);
        });
    }

    /**
     * Sets the process to be archived (soft deleted)
     * @return ProcessFactory
     */
    public function archived() {
        return $this->state(function () {
            return [
                'deleted_at' => now(),
                'visibility' => Visibility::Private->value
            ];
        });
    }

    /**
     * Process is created with template.
     * @param ProcessVersion|null $template
     * @return ProcessFactory
     */
    public function withTemplate($template = null) {
        if ($template === null) {
            $template = ProcessVersion::factory()->create();
        }

        return $this->afterCreating(function (Process $process) use ($template) {
            $process->template()->associate($template)->save();
        });
    }

    /**
     * Process is created with a license.
     * @param array|Collection|License|null $licenses
     * @return ProcessFactory
     */
    public function withLicense(array|Collection|License $licenses = null) {
        if ($licenses === null) {
            $licenses = [License::factory()->create(), License::factory()->create()];
        }
        else if ($licenses instanceof License) {
            $licenses = [$licenses];
        }
        else {
            foreach ($licenses as $license) {
                if (!$license instanceof License) {
                    throw new InvalidArgumentException('All elements in the $licenses ' . (is_array($licenses) ? 'array' : $licenses::class) . ' must be instances of ' . License::class);
                }
            }
        }

        return $this->afterCreating(function (Process $process) use ($licenses) {
            $process->licenses()->saveMany($licenses);
        });
    }

    /**
     * Adds tags to the process.
     * @param array|Collection|string|null $tags
     * @return $this
     */
    public function withTags(array|Collection|string $tags = null) {
        if ($tags === null) {
            $tags = ['foo', 'bar'];
        }
        else if (is_string($tags)) {
            $tags = [$tags];
        }
        else {
            foreach ($tags as $tag) {
                if (!is_string($tag)) {
                    throw new InvalidArgumentException('All elements in the $licenses ' . (is_array($tags) ? 'array' : $tags::class) . ' must be instances of string');
                }
            }
        }

        return $this->afterCreating(function (Process $process) use ($tags) {
            $process->tags()->saveMany(collect($tags)->map(fn($tag) => Tag::factory()->name($tag)->create()));
        });
    }

    /**
     * Adds a running simulation to the process.
     * @param array|Collection|Simulation|null $simulations
     * @return ProcessFactory
     */
    public function withSimulation(array|Collection|Simulation $simulations = null) {
        if ($simulations === null) {
            return $this->afterCreating(function (Process $process) {
                $simulations = Simulation::factory()->count(2)->ofProcess($process)->create();
                $process->simulations()->saveMany($simulations);
            });
        }
        else if ($simulations instanceof Simulation) {
            $simulations = [$simulations];
        }
        else {
            foreach ($simulations as $simulation) {
                if (!is_string($simulation)) {
                    throw new InvalidArgumentException('All elements in the $simulations ' . (is_array($simulations) ? 'array' : $simulations::class) . ' must be instances of ' . Simulation::class);
                }
            }
        }

        return $this->afterCreating(function (Process $process) use ($simulations) {
            $process->simulations()->saveMany($simulations);
        });
    }

    /**
     * Process is created with definition.
     * @param array|Collection|Definition|null $definitions
     * @return ProcessFactory
     */
    public function withDefinition(array|Collection|Definition $definitions = null) {
        if ($definitions === null) {
            $definitions = [new Definition([]), new Definition([])];
        }
        else if ($definitions instanceof Definition) {
            $definitions = [$definitions];
        }
        else {
            foreach ($definitions as $definition) {
                if (!is_string($definition)) {
                    throw new InvalidArgumentException('All elements in the $definitions ' . (is_array($definitions) ? 'array' : $definitions::class) . ' must be instances of ' . Definition::class);
                }
            }
        }

        return $this->afterCreating(function (Process $process) use ($definitions) {
            $process->definitions()->saveMany($definitions);
        });
    }

    /**
     * Process is created with process version.
     * @param Collection|ProcessVersion[] $versions
     * @return ProcessFactory
     */
    public function withProcessVersion($versions = []) {
        if (empty($versions)) {
            $versions = [ProcessVersion::factory()->create()];
        }
        else if ($versions instanceof ProcessVersion) {
            $versions = [$versions];
        }
        else {
            foreach ($versions as $version) {
                if (!$version instanceof ProcessVersion) {
                    throw new InvalidArgumentException('All elements in the $versions ' . (is_array($versions) ? 'array' : $versions::class) . ' must be instances of ' . ProcessVersion::class);
                }
            }
        }

        return $this->afterCreating(function (Process $process) use ($versions) {
            $process->versions()->saveMany($versions);
        });
    }

    /**
     * Sets the latest published version id to null.
     * @return ProcessFactory
     */
    public function withoutLatestPublishedVersion() {
        return $this->state(function () {
            return [
                'latest_published_version_id' => null
            ];
        });
    }

    /**
     * Process is created with a last process version.
     * @param ProcessVersion|null $processVersion
     * @return ProcessFactory
     */
    public function withLatestVersion(ProcessVersion $processVersion = null) {
        return $this->state(fn() => ['latest_published_version_id' => null])
            ->afterCreating(function (Process $process) use ($processVersion) {
                $processVersion = $processVersion ?? ProcessVersion::factory()->ofProcess($process)->create([
                    'full_namespace' => $process->full_namespace . '@develop',
                    'version' => 'develop',
                    'definition->namespace' => $process->namespace,
                    'definition->identifier' => $process->identifier,
                    'definition->version' => 'develop',
                    'published_at' => null
                ]);
                $process->versions()->save($processVersion);
                $process->latestVersion()->associate($processVersion);
            });
    }

    /**
     * Process is created with a latest published process version.
     * @param ProcessVersion|null $processVersion
     * @return ProcessFactory
     */
    public function withLatestPublishedVersion(ProcessVersion $processVersion = null) {
        return $this->state(fn() => [
            'latest_version_id' => Uuid::uuid4()->toString(),
            'latest_version' => '1.0.0'
        ])->afterCreating(function (Process $process) use ($processVersion) {
            $processVersion = $processVersion ?? ProcessVersion::factory()->create([
                'full_namespace' => $process->full_namespace . '@1.0.0',
                'version' => '1.0.0',
                'definition->namespace' => $process->namespace,
                'definition->identifier' => $process->identifier,
                'definition->version' => '1.0.0',
                'published_at' => now()
            ]);
            $process->versions()->save($processVersion);
            $process->latestPublishedVersion()->associate($processVersion);
            $process->update(['latest_published_version_id' => $processVersion->id]);
        });
    }

    /**
     * Process is created with a published process version (not set as latest)
     * @param ProcessVersion|null $processVersion
     * @return ProcessFactory
     */
    public function withPublishedVersion(ProcessVersion $processVersion = null) {
        return $this->state(fn() => [
            'latest_version_id' => Uuid::uuid4()->toString(),
            'latest_version' => '9.9.9',
            'latest_published_version_id' => Uuid::uuid4()->toString()
        ])->afterCreating(function (Process $process) use ($processVersion) {
            $processVersion = $processVersion ?? ProcessVersion::factory()->create([
                'full_namespace' => $process->full_namespace . '@1.0.0',
                'version' => '1.0.0',
                'definition->namespace' => $process->namespace,
                'definition->identifier' => $process->identifier,
                'definition->version' => '1.0.0',
                'published_at' => now()
            ]);

            $process->versions()->save($processVersion);
        });
    }

    /**
     * Sets the visibility of the process.
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
}