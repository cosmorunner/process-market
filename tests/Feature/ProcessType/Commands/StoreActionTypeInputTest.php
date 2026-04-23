<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreActionTypeInput;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\InputBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreActionTypeInputTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreActionTypeInputTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_action_type_input_simple() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = array_merge(['action_type_id' => $actionType->id], app(InputBuilder::class)->make()->toArray());

        $this->assertCount(0, $definition->actionTypes->first()->inputs);
        $definition = (new StoreActionTypeInput($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->actionTypes->first()->inputs);
    }

}
