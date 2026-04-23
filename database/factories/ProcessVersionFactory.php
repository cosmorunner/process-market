<?php

namespace Database\Factories;

use App\Models\Environment;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use App\Models\Simulation;
use App\Models\Synchronization;
use App\Models\User;
use App\ProcessType\Definition;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class GraphFactory
 * @package Database\Factories
 */
class ProcessVersionFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = ProcessVersion::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        $namespace = $this->faker->word;
        $identifier = $this->faker->word . '-test';

        return [
            'process_id' => $this->faker->uuid,
            'calculated' => config('graph.empty_calculated'),
            'definition' => array_merge(config('graph.empty'), [
                'namespace' => $namespace,
                'identifier' => $identifier
            ]),
            'version' => 'develop',
            'changelog' => null,
            'complexity_score' => $this->faker->randomFloat(2, 1, 5),
            'full_namespace' => $namespace . '/' . $identifier . '@develop',
            'updated_by' => null,
            'published_at' => null,
            'published_by' => null
        ];
    }

    /**
     * Markt the process version as published.
     * @return ProcessVersionFactory
     */
    public function isPublished(User $publisher = null) {
        $publisher = $publisher ?? User::factory()->create();

        return $this->state(function () use ($publisher) {
            return [
                'updated_by' => $publisher->id,
                'published_at' => Carbon::now(),
                'published_by' => $publisher->id,
                'version' => '1.0.0',
                'definition->version' => '1.0.0'
            ];
        });
    }

    /**
     * @param string $templateName
     * @return ProcessVersionFactory
     */
    public function ofTemplate(string $templateName) {
        $definition = config('graph.' . $templateName);

        if (!is_array($definition)) {
            return $this;
        }

        return $this->state(function ($definition) {
            return [
                'definition' => $definition
            ];
        });
    }


    /**
     * Sets the version.
     * @param string $version
     * @return ProcessVersionFactory
     */
    public function ofVersion(string $version) {
        return $this->state(function () use ($version) {
            return [
                'version' => $version,
                'definition->version' => $version
            ];
        });
    }

    /**
     * Adds a specfic definition to the process version.
     * @param Definition $definition
     * @return ProcessVersionFactory
     */
    public function withDefinition(Definition $definition) {
        return $this->state(function () use ($definition) {
            return [
                'definition' => $definition->toArray()
            ];
        });
    }

    /**
     * Adds a specfic changelog to the process version.
     * @param string|null $changelog
     * @return ProcessVersionFactory
     */
    public function withChangelog(string $changelog = null) {
        return $this->state(function () use ($changelog) {
            return [
                'changelog' => $changelog ?? 'I am a change.'
            ];
        });
    }

    /**
     * Adds specfic demo data to the process version.
     * @param array|null $demoData
     * @return ProcessVersionFactory
     */
    public function withDemoData(array $demoData = null) {
        return $this->state(function () use ($demoData) {
            $demoData = $demoData ?? [
                [
                    'action_type_id' => $this->faker->uuid,
                    'name' => $this->faker->word,
                    'values' => ['foo' => 'bar']
                ]
            ];

            return [
                'demo_data' => $demoData ?? 'I am a change.'
            ];
        });
    }


    /**
     * Adds dependencies to the process version.
     * @param array $dependencies
     * @return ProcessVersionFactory
     */
    public function withDependencies($dependencies = []) {
        return $this->afterCreating(function (ProcessVersion $processVersion) use ($dependencies) {
            if (empty($dependencies)) {
                $dependencies = ['process_types' => ['foo/bar@1.0.0']];
            }

            $processVersion->update(['definition->dependencies' => $dependencies]);
        });
    }

    /**
     * Adds history head to the process version.
     * @param ProcessVersionHistory $history
     * @return ProcessVersionFactory
     */
    public function withHistoryHead($history = null) {
        return $this->afterCreating(function (ProcessVersion $processVersion) use ($history) {
            if ($history === null) {
                $history = ProcessVersionHistory::factory()->ofProcessVersion($processVersion)->create();
            }

            $processVersion->update([
                'history_head' => $history->id,
            ]);
            $processVersion->historyHead()->save($history);
        });
    }

    /**
     * Adds previous history to the process version.
     * @param ProcessVersionHistory $history
     * @return ProcessVersionFactory
     */
    public function withPreviousHistory($history = []) {
        return $this->afterCreating(function (ProcessVersion $processVersion) use ($history) {
            if (empty($history)) {
                $history = [
                    ProcessVersionHistory::factory()->ofProcessVersion($processVersion)->create([
                        'created_at' => Carbon::now()->subWeek()
                    ]),
                    ProcessVersionHistory::factory()->ofProcessVersion($processVersion)->create([
                        'created_at' => Carbon::now()->subWeek()
                    ])
                ];
            }
            $processVersion->previousHistory()->saveMany($history);
        });
    }

    /**
     * Adds succeeding history to the process version.
     * @param ProcessVersionHistory $history
     * @return ProcessVersionFactory
     */
    public function withSucceedingHistory($history = []) {
        return $this->afterCreating(function (ProcessVersion $processVersion) use ($history) {
            if (empty($history)) {
                $history = [
                    ProcessVersionHistory::factory()->ofProcessVersion($processVersion)->create([
                        'created_at' => Carbon::now()->addWeek()
                    ]),
                    ProcessVersionHistory::factory()->ofProcessVersion($processVersion)->create([
                        'created_at' => Carbon::now()->addWeek()
                    ])
                ];
            }
            $processVersion->succeedingHistory()->saveMany($history);
        });
    }

    /**
     * Sets the process of the process version
     * @param Process $process
     * @param bool $setAsLatest Sets the process version as the latest (most current) version of the process
     * @return ProcessVersionFactory
     */
    public function ofProcess($process, $setAsLatest = true): ProcessVersionFactory {
        return $this->afterCreating(function (ProcessVersion $processVersion) use ($process, $setAsLatest) {
            $processVersion->update([
                'full_namespace' => $process->full_namespace . '@' . $processVersion->version,
                'definition->namespace' => $process->namespace,
                'definition->identifier' => $process->identifier,
                'definition->version' => $processVersion->version
            ]);

            $processVersion->process()->associate($process)->save();

            if ($setAsLatest) {
                $process->update(['latest_version_id' => $processVersion->id]);
            }

            if ($processVersion->isPublished()) {
                $process->update(['latest_published_version_id' => $processVersion->id]);
            }
        });
    }

    /**
     * Sets an unspecific process of the process version.
     * @param $setAsLatest
     * @return ProcessVersionFactory
     */
    public function withProcess($setAsLatest = true) {
        return $this->afterCreating(function (ProcessVersion $processVersion) use ($setAsLatest) {
            $process = Process::factory()->create();
            $process->update([
                'namespace' => $processVersion->definition->namespace,
                'identifier' => $processVersion->definition->identifier,
                'full_namespace' => $processVersion->definition->namespace . '/' . $processVersion->definition->identifier,
            ]);

            $processVersion->process()->associate($process)->save();

            if ($setAsLatest) {
                $process->update(['latest_version_id' => $processVersion->id]);
            }

            if ($processVersion->isPublished()) {
                $process->update(['latest_published_version_id' => $processVersion->id]);
            }
        });
    }

    /**
     * Adds simulation to the process version.
     * @param Collection|Simulation[] $simulation
     * @return ProcessVersionFactory
     */
    public function withSimulation(array $simulation = []) {
        if (empty($simulation)) {
            $simulation = Simulation::factory()->count(2)->create();
        }

        return $this->afterCreating(function (ProcessVersion $processVersion) use ($simulation) {
            $processVersion->simulations()->saveMany($simulation);
        });
    }

    /**
     * Adds simulation and process to the process version.
     * @param Process $process
     * @param User|null $user
     * @return ProcessVersionFactory
     */
    public function withSimulationAndProcess($process = null, $user = null) {
        if ($process === null) {
            $process = Process::factory()->create();
        }
        if ($user === null) {
            $user = User::factory()->create();
        }

        return $this->afterCreating(function (ProcessVersion $processVersion) use ($process) {
            $processVersion->process()->associate($process)->save();
        })->afterCreating(function (ProcessVersion $processVersion) use ($user) {
            $simulation = Simulation::factory()->ofProcessVersion($processVersion)->ofUser($user)->create();
            $processVersion->simulations()->save($simulation);
        });
    }


    /**
     * Adds synchronisation to the process version.
     * @param Collection|Synchronization[] $synchronisation
     * @return ProcessVersionFactory
     */
    public function withSynchronization(array $synchronisation = []) {
        return $this->afterCreating(function (ProcessVersion $processVersion) use ($synchronisation) {

            if (empty($synchronisation)) {
                $synchronisation = [Synchronization::factory()->ofSubject($processVersion)->create()];
            }

            $processVersion->synchronizations()->saveMany($synchronisation);
        });
    }

    /**
     * Adds environment to the process version.
     * @param Collection|Environment[] $environments
     * @param bool $default
     * @return ProcessVersionFactory
     */
    public function withEnvironments(array $environments = [], bool $default = false): ProcessVersionFactory {
        return $this->afterCreating(function (ProcessVersion $processVersion) use ($environments, $default) {
            if (empty($environments)) {
                $environments = [Environment::factory()->withProcessVersion($processVersion)->create(['default' => $default])];
            }

            $processVersion->environments()->saveMany($environments);
        });
    }
}