<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreActionTypeOutput;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\OutputBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreActionTypeOutputTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreActionTypeOutputTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_store_action_type_output_simple() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = array_merge(['action_type_id' => $actionType->id], app(OutputBuilder::class)->make()->toArray());

        $this->assertCount(0, $definition->actionTypes->first()->outputs);
        $definition = (new StoreActionTypeOutput($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->actionTypes->first()->outputs);
    }

    public function test_commands_store_action_type_output_with_input_field() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();
        $outputArray = app(OutputBuilder::class)->make()->toArray();

        $payload = array_merge(['action_type_id' => $actionType->id], $outputArray, ['include_in_input_data' => true]);
        $definition = (new StoreActionTypeOutput($payload, $definition, $processVersion))->execute();
        $actionType = $definition->actionTypes->first();
        $this->assertNotNull($actionType->input($payload['name']));
    }

    public function test_commands_store_action_type_load_process_data_field() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();
        $outputArray = app(OutputBuilder::class)->make()->toArray();

        $payload = array_merge(['action_type_id' => $actionType->id], $outputArray, ['include_in_input_data' => true,'load_process_data_field' => true]);
        $definition = (new StoreActionTypeOutput($payload, $definition, $processVersion))->execute();
        $actionType = $definition->actionTypes->first();
        $input = $actionType->input($payload['name']);
        $this->assertTrue(str_starts_with($input->value, '[[process.outputs.' . $payload['name']));
    }
}
