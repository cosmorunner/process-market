<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateComponent;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\ActionTypeComponentBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateComponentTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateComponentTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $component = app(ActionTypeComponentBuilder::class)->make();
        $actionType = app(ActionTypeBuilder::class)->withComponents([$component])->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('Component 1', $component->label);

        $payload = array_merge($actionType->components->first()->toArray(), ['label' => 'new bar']);

        $definition = (new UpdateComponent($payload, $definition, $processVersion))->execute();

        $this->assertEquals('new bar', $definition->actionTypes->first()->components->first()->label);
    }

}
