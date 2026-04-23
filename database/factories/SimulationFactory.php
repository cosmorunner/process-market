<?php

namespace Database\Factories;

use App\Models\ProcessVersion;
use App\Models\Process;
use App\Models\Simulation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SimulationFactory
 * @package Database\Factories
 */
class SimulationFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Simulation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'id' => $this->faker->uuid,
            'user_id' => $this->faker->uuid,
            'process_id' => $this->faker->uuid,
            'process_version_id' => $this->faker->uuid,
            'environment_id' => $this->faker->uuid,
            'token' => $this->faker->text(200),
            'allisa_id' => $this->faker->uuid,
            'allisa_process_type_id' => $this->faker->uuid,
            'allisa_user_id' => $this->faker->uuid,
            'finished_at' => null
        ];
    }

    /**
     * Setzt den Zeitpunkt des Beendens der Simulation
     * @param Carbon|null $carbon
     * @return SimulationFactory
     */
    public function finished(Carbon $carbon = null) {
        return $this->state(function () use ($carbon) {
            return [
                'finished_at' => $carbon ?? now(),
            ];
        });
    }

    /**
     * Setzt den Benutzer, der die Simulation gestartet hat.
     * @param User|null $user
     * @return $this
     */
    public function ofUser(User $user = null) {
        return $this->state(function () use ($user) {
            return [
                'user_id' => $user->id ?? null,
            ];
        });
    }

    /**
     * Entfernt den Benutzer. Die Simulation wurde von einem Besucher des Marketplatztes gestartet.
     * @return SimulationFactory
     */
    public function anonymous() {
        return $this->state(function () {
            return [
                'user_id' => null,
            ];
        });
    }

    /**
     * Setzt den Prozess, von dem eine Simulationg gestartet wurde.
     * @param Process $process
     * @return SimulationFactory
     */
    public function ofProcess(Process $process) {
        return $this->state(function () use ($process) {
            return [
                'process_id' => $process->id,
            ];
        });
    }

    /**
     * Setzt den Graph, von dem eine Simulationg gestartet wurde.
     * @param ProcessVersion $processVersion
     * @return SimulationFactory
     */
    public function ofProcessVersion(ProcessVersion $processVersion) {
        return $this->state(function () use ($processVersion) {
            return [
                'process_id' => $processVersion->process->id,
                'process_version_id' => $processVersion->id,
            ];
        });
    }

}
