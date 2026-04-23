<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreProcessTypeOutput;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\OutputBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreProcessTypeOutputTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreProcessTypeOutputTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_store_process_type_output_simple() {
        $output = app(OutputBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = $output->toArray();

        $this->assertCount(0, $definition->outputs);
        $definition = (new StoreProcessTypeOutput($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->outputs);
    }

}
