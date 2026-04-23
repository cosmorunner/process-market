<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteActionTypeOutput;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\OutputBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteActionTypeOutputTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteActionTypeOutputTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $output = app(OutputBuilder::class)->make();
        $actionType = app(ActionTypeBuilder::class)->withOutputs([$output])->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'name' => $actionType->outputs->first()->name
        ];

        $this->assertCount(1, $actionType->outputs);

        (new DeleteActionTypeOutput($payload, $definition, $processVersion))->execute();

        $this->assertCount(0, $definition->actionTypes->first()->outputs);
    }

}
