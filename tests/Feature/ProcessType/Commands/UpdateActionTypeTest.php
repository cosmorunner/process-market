<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateActionType;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateActionTypeTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateActionTypeTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $actionType = app(ActionTypeBuilder::class)->make(['name' => 'Dummy']);
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('Dummy', $definition->actionTypes->first()->name);

        $payload = array_merge($actionType->toArray(), ['name' => 'New Name']);

        $definition = (new UpdateActionType($payload, $definition, $processVersion))->execute();

        $this->assertEquals('New Name', $definition->actionTypes->first()->name);
    }

}
