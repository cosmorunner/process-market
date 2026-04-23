<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteProcessTypeOutput;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\OutputBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteProcessTypeOutputTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteProcessTypeOutputTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_delete_process_type_output_simple() {
        $output = app(OutputBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withOutputs([$output])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'name' => $definition->outputs->first()->name
        ];

        $this->assertCount(1, $definition->outputs);
        (new DeleteProcessTypeOutput($payload, $definition, $processVersion))->execute();
        $this->assertCount(0, $definition->outputs);
    }

}
