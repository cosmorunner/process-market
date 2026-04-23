<?php

namespace Database\Factories;

use App\Environment\Blueprint;
use App\Models\Environment;
use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EnvironmentFactory
 * @method Environment make(array $attributes = [], ?Model $parent = null)
 * @package Database\Factories
 */
class EnvironmentFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Environment::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        return [
            'name' => 'Standard',
            'description' => $this->faker->text,
            'process_version_id' => $this->faker->uuid,
            'initial_action_type_id' => $this->faker->uuid,
            'query_context' => $this->faker->text,
            'default' => false,
            'public' => false,
            'blueprint' => Blueprint::make(),
        ];
    }

    /**
     * Creates an environment with a specific name and optional specific blueprint.
     * @param string $name
     * @param Blueprint|null $blueprint
     * @return EnvironmentFactory
     */
    public function emptyWithName(string $name, Blueprint $blueprint = null) {
        return $this->state(fn() => [
            'name' => $name,
            'description' => '',
            'process_version_id' => '',
            'initial_action_type_id' => '',
            'query_context' => '',
            'default' => false,
            'public' => false,
            'blueprint' => $blueprint ?? Blueprint::make(),
        ]);
    }

    /**
     * Marks the environment as default (or not).
     * @param bool|null $flag
     * @return EnvironmentFactory
     */
    public function isDefault(bool $flag = true) {
        return $this->state(fn() => ['default' => $flag]);
    }

    /**
     * Marks the environment as default (or not).
     * @param bool|null $flag
     * @return EnvironmentFactory
     */
    public function isPublic(bool $flag = true) {
        return $this->state(fn() => ['public' => $flag]);
    }

    /**
     * Sets the initial actiontype of the environment.
     * @param ActionType|string $actionType
     * @return EnvironmentFactory
     */
    public function withInitialActionType(ActionType|string $actionType) {
        return $this->state(fn() => ['initial_action_type_id' => $actionType instanceof ActionType ? $actionType->id : $actionType]);
    }

    /**
     * Sets a dummy process version of the environment.
     * @param ProcessVersion|null $processVersion
     * @return EnvironmentFactory
     */
    public function withProcessVersion(ProcessVersion $processVersion = null) {
        if ($processVersion === null) {
            $processVersion = ProcessVersion::factory()->create();
        }

        return $this->afterCreating(function (Environment $environment) use ($processVersion) {
            $environment->processVersion()->associate($processVersion)->save();
        });
    }

    /**
     * Adds a blueprint to the environment.
     * @param Blueprint $blueprint
     * @return EnvironmentFactory
     */
    public function withBlueprint(Blueprint $blueprint) {
        return $this->state(fn() => ['blueprint' => $blueprint->toArray()]);
    }
}
