<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreState;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\StateBuilder;
use Database\Builder\Definition\StatusTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreStateTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreStateTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_store_state_simple() {
        $statusType = app(StatusTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = app(StateBuilder::class)->make(['status_type_id' => $statusType->id])->toArray();

        $this->assertCount(0, $definition->statusTypes->first()->states);
        $definition = (new StoreState($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->statusTypes->first()->states);
    }

    public function test_commands_store_state_only_description_no_initial_state() {
        $statusType = app(StatusTypeBuilder::class)->withInitialValue('10.000')->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        // Since the status type does not have any state, the new state should get the statustype default (initial) value.
        $payload = [
            'status_type_id' => $statusType->id,
            'description' => 'NewState1',
        ];

        $definition = (new StoreState($payload, $definition, $processVersion))->execute();
        $this->assertEquals('10.000', $definition->statusType($statusType->id)->states->first()->min);
        $this->assertEquals('10.000', $definition->statusType($statusType->id)->states->first()->max);
    }

    public function test_commands_store_state_only_description_with_initial_state() {
        $initialState = app(StateBuilder::class)->withMin('10.000')->withMax('10.000')->make();
        $statusType = app(StatusTypeBuilder::class)->withInitialValue('10.000')->withStates([$initialState])->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        // Since the status type does already have an initial state, "NewState1" should get the max value + 1 (11).
        $payload = [
            'status_type_id' => $statusType->id,
            'description' => 'NewState1',
        ];

        $definition = (new StoreState($payload, $definition, $processVersion))->execute();
        $newState = $definition->statusType($statusType->id)->states->firstWhere('description', 'NewState1');
        $this->assertEquals('11.000', $newState->min);
        $this->assertEquals('11.000', $newState->max);
    }

    public function test_commands_store_state_with_custom_value_no_initial_state() {
        $statusType = app(StatusTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        // Since the status type does already have an initial state, "NewState1" should get the max value + 1 (11).
        $payload = [
            'status_type_id' => $statusType->id,
            'description' => 'NewState1',
            'min' => '20'
        ];

        $definition = (new StoreState($payload, $definition, $processVersion))->execute();
        $newState = $definition->statusType($statusType->id)->states->firstWhere('description', 'NewState1');
        $this->assertEquals('20.000', $newState->min);
        $this->assertEquals('20.000', $newState->max);
    }

    public function test_commands_store_state_with_custom_value_with_initial_state() {
        $statusType = app(StatusTypeBuilder::class)->withStates([10])->withInitialValue('10.000')->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        // Since the status type does already have an initial state, "NewState1" should get the max value + 1 (11).
        $payload = [
            'status_type_id' => $statusType->id,
            'description' => 'NewState1',
            'min' => '20'
        ];

        $definition = (new StoreState($payload, $definition, $processVersion))->execute();
        $newState = $definition->statusType($statusType->id)->states->firstWhere('description', 'NewState1');
        $this->assertEquals('20.000', $newState->min);
        $this->assertEquals('20.000', $newState->max);
    }

}
