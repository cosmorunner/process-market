<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateActionTypeOutput;
use App\ProcessType\Output;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateActionTypeOutputTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateActionTypeOutputTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $output = Output::make(['description' => 'foobar']);
        $actionType = app(ActionTypeBuilder::class)->withOutputs([$output])->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $output->description);

        $payload = array_merge($output->toArray(), [
            'old_name' => $output->name,
            'action_type_id' => $actionType->id,
            'description' => 'new foobar',
        ]);

        $definition = (new UpdateActionTypeOutput($payload, $definition, $processVersion))->execute();

        $this->assertEquals('new foobar', $definition->actionTypes->first()->outputs->first()->description);
    }

}
