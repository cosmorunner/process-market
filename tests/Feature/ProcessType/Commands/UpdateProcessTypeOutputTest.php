<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateProcessTypeOutput;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\OutputBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateProcessTypeOutputTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateProcessTypeOutputTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $output = app(OutputBuilder::class)->make(['description' => 'foobar']);
        $definition = app(DefinitionBuilder::class)->withOutputs([$output])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $output->description);

        $updatedOutput = array_merge($output->toArray(), [
            'old_name' => $output->name,
            'description' => 'new foobar'
        ]);

        $definition = (new UpdateProcessTypeOutput($updatedOutput, $definition, $processVersion))->execute();

        $this->assertEquals('new foobar', $definition->outputs->first()->description);
    }

}
