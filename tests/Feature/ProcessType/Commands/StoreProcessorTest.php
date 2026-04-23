<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreProcessor;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ProcessorBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreProcessorTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreProcessorTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = app(ProcessorBuilder::class)->make([
            'identifier' => 'update_process_meta',
            'action_type_id' => $actionType->id,
        ])->toArray();

        $this->assertCount(0, $definition->actionTypes->first()->processors);
        $definition = (new StoreProcessor($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->actionTypes->first()->processors);
    }

}
