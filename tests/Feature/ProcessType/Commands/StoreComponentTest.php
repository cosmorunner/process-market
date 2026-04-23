<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreComponent;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\ActionTypeComponentBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreComponentTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreComponentTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_store_component_simple() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $component = app(ActionTypeComponentBuilder::class)->ofActionType($actionType)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = $component->toArray();

        $this->assertCount(0, $actionType->components);
        $definition = (new StoreComponent($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->actionType($actionType->id)->components);
    }

}
