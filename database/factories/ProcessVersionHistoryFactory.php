<?php

namespace Database\Factories;

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 *
 */
class ProcessVersionHistoryFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = ProcessVersionHistory::class;

    /**
     * Define the model's default state.
     *-+
     * >6
     * @return array
     */
    public function definition() {
        return [
            'process_version_id' => ProcessVersion::factory()->create()->id,
            'command' => $this->faker->word,
            'command_payload' => ['test', 'test2'],
            'calculated' => ['test', 'test2'],
            'definition' => app(DefinitionBuilder::class)->make()->toArray()
        ];
    }

    /**
     * Sets the process version.
     * @param ProcessVersion $processVersion
     * @return ProcessVersionHistoryFactory
     */
    public function ofProcessVersion($processVersion = null) {
        if ($processVersion === null) {
            $processVersion = ProcessVersion::factory()->create();
        }

        return $this->afterCreating(function (ProcessVersionHistory $processVersionHistory) use ($processVersion) {
            $processVersionHistory->processVersion()->associate($processVersion)->save();
        });
    }
}