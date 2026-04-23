<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreStateBulk;
use App\ProcessType\State;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\StateBuilder;
use Database\Builder\Definition\StatusTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreStateBulkTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreStateBulkTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_store_state_bulk_rule_without_min_max() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->withAnyStatusTypes()->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'status_type_id' => $definition->statusTypes->first()->id,
            'value' => ['NewState1']
        ];

        $this->updateDefinition($processVersion, 'StoreStateBulk', $payload)
            ->assertOk()
            ->assertJsonMissingValidationErrors(['min', 'max']);
    }

    public function test_commands_store_state_bulk_rule_invalid_min() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->withAnyStatusTypes()->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'status_type_id' => $definition->statusTypes->first()->id,
            'value' => ['NewState1;abc']
        ];

        $responseArray = $this->updateDefinition($processVersion, 'StoreStateBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "min" error key exists in the 0-index row.
        $this->assertArrayHasKey('min', $responseArray['errors']['value.0']);
    }

    public function test_commands_store_state_bulk_rule_invalid_max() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->withAnyStatusTypes()->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'status_type_id' => $definition->statusTypes->first()->id,
            // "abc" is an invalid max value.
            'value' => ['NewState1;5;abc']
        ];

        $responseArray = $this->updateDefinition($processVersion, 'StoreStateBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "max" error key exists in the 0-index row.
        $this->assertArrayHasKey('max', $responseArray['errors']['value.0']);
    }

    public function test_commands_store_state_bulk_rule_overlapping_min_max_within_existing_states() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $state = app(StateBuilder::class)->withMin('10.000')->withMax('20.000')->make();
        $statusType = app(StatusTypeBuilder::class)->withStates([$state])->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'status_type_id' => $definition->statusTypes->first()->id,
            // 15 is invalid, because the statustype has a stage with range 10-20.
            'value' => ['NewState1;15']
        ];

        $responseArray = $this->updateDefinition($processVersion, 'StoreStateBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "min" error key exists in the 0-index row.
        $this->assertArrayHasKey('min', $responseArray['errors']['value.0']);
    }

    public function test_commands_store_state_bulk_rule_overlapping_min_max_within_other_rows() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $statusType = app(StatusTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'status_type_id' => $definition->statusTypes->first()->id,
            // Value ranges overlap
            'value' => [
                'NewState1;15',
                'NewState2;10;20',
            ]
        ];

        $this->updateDefinition($processVersion, 'StoreStateBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value');
    }

    public function test_commands_store_state_bulk_without_min_max() {
        $statusType = app(StatusTypeBuilder::class)->withInitialValue('10.000')->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'status_type_id' => $statusType->id,

            // No min/max provided. Should be set to default value of statustype.
            'value' => ['NewState1']
        ];

        $definition = (new StoreStateBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusType($statusType->id);
        $this->assertEquals($statusType->default, $statusType->states->first()->min);
        $this->assertEquals($statusType->default, $statusType->states->first()->max);
    }

    public function test_commands_store_state_bulk_only_min() {
        $statusType = app(StatusTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'status_type_id' => $statusType->id,

            // Only min provided and statustype does not have states. State min/max should be set to min value.
            'value' => ['NewState1;20']
        ];

        $definition = (new StoreStateBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusType($statusType->id);

        $this->assertNotEquals($statusType->default, $statusType->states->first()->min);
        $this->assertEquals('20.000', $statusType->states->first()->min);
        $this->assertEquals('20.000', $statusType->states->first()->max);
    }

    public function test_commands_store_state_bulk_with_min_max() {
        $statusType = app(StatusTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'status_type_id' => $statusType->id,

            // Only min provided and statustype does not have states. State min/max should be set to min value.
            'value' => ['NewState1;20;30']
        ];

        $definition = (new StoreStateBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusType($statusType->id);

        $this->assertNotEquals($statusType->default, $statusType->states->first()->min);
        $this->assertEquals('20.000', $statusType->states->first()->min);
        $this->assertEquals('30.000', $statusType->states->first()->max);
    }

    public function test_commands_store_state_bulk_min_max_set_to_max_plus_one_with_only_initial_state() {
        $state = app(StateBuilder::class)->withMin('10.000')->withMax('10.000')->make();

        // Initial state min/max is set to 10.000.
        $statusType = app(StatusTypeBuilder::class)->withInitialValue('10.000')->withStates([$state])->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        // No min/max provided, but status type already has initial state. Should be set to max value + 1 of existing state.
        $payload = [
            'status_type_id' => $statusType->id,
            'value' => ['NewState1']
        ];

        $definition = (new StoreStateBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusType($statusType->id);

        /* @var State $newState1 */
        $newState1 = $statusType->states->firstWhere('description', '=', 'NewState1');

        // 10.000 of default value + 1.
        $this->assertEquals('11.000', $newState1->min);
        $this->assertEquals('11.000', $newState1->max);

        //
        // Test again with decimal value
        //
        $state = app(StateBuilder::class)->withMin('10.2')->withMax('10.2')->make();
        $statusType = app(StatusTypeBuilder::class)->withInitialValue('10.2')->withStates([$state])->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        // No min/max provided, but status type already has initial state. Should be set to rounded max value of existing default state (10) + 1.
        $payload = [
            'status_type_id' => $statusType->id,
            'value' => ['NewState1']
        ];

        $definition = (new StoreStateBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusType($statusType->id);

        /* @var State $newState1 */
        $newState1 = $statusType->states->firstWhere('description', '=', 'NewState1');
        $this->assertEquals('11.000', $newState1->min);
        $this->assertEquals('11.000', $newState1->max);
    }

    public function test_commands_store_state_bulk_multiple_states_without_min_max() {
        $state = app(StateBuilder::class)->withMin('10.000')->withMax('10.000')->make();
        $statusType = app(StatusTypeBuilder::class)->withInitialValue('10.000')->withStates([$state])->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'status_type_id' => $statusType->id,

            // No min/max provided, but status type already has initial state. Should be set to max value + 1 of existing state.
            'value' => [
                'NewState1',
                'NewState2',
            ]
        ];

        $definition = (new StoreStateBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusType($statusType->id);

        /* @var State $newState1 */
        /* @var State $newState2 */
        $newState1 = $statusType->states->firstWhere('description', '=', 'NewState1');
        $newState2 = $statusType->states->firstWhere('description', '=', 'NewState2');

        $this->assertEquals('11.000', $newState1->min);
        $this->assertEquals('12.000', $newState2->min);
    }

    public function test_commands_store_state_bulk_state_with_min_value() {
        $statusType = app(StatusTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'status_type_id' => $statusType->id,

            // Only min provided. Max should equal min.
            'value' => ['NewState1;100']
        ];

        $definition = (new StoreStateBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusType($statusType->id);

        /* @var State $newState1 */
        /* @var State $newState2 */
        $newState1 = $statusType->states->firstWhere('description', '=', 'NewState1');

        $this->assertEquals('100.000', $newState1->min);
        $this->assertEquals('100.000', $newState1->max);
    }

    public function test_commands_store_state_bulk_state_with_min_max_value() {
        $statusType = app(StatusTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'status_type_id' => $statusType->id,

            // Only min provided. Max should equal min.
            'value' => ['NewState1;100;200']
        ];

        $definition = (new StoreStateBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusType($statusType->id);

        /* @var State $newState1 */
        /* @var State $newState2 */
        $newState1 = $statusType->states->firstWhere('description', '=', 'NewState1');

        $this->assertEquals('100.000', $newState1->min);
        $this->assertEquals('200.000', $newState1->max);
    }

    public function test_commands_store_state_bulk_state_with_min_and_missing_min_value() {
        $statusType = app(StatusTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'status_type_id' => $statusType->id,

            // Since one item has a min value, the "NewState2" state should have its min value set to 101.
            'value' => [
                'NewState1;100',
                'NewState2',
            ]
        ];

        $definition = (new StoreStateBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusType($statusType->id);

        /* @var State $newState2 */
        $newState2 = $statusType->states->firstWhere('description', '=', 'NewState2');

        $this->assertEquals('101.000', $newState2->min);
        $this->assertEquals('101.000', $newState2->max);
    }

    public function test_commands_store_state_bulk_state_min_value_is_calculated_with_existing_states_and_more_rows() {
        // Initial state 10
        $initialState = app(StateBuilder::class)->withMin('10.000')->withMax('10.000')->make();
        $state = app(StateBuilder::class)->withMin('20.000')->withMax('20.000')->make();

        // Two existing states 10 and 20.
        $statusType = app(StatusTypeBuilder::class)->withInitialValue('10.000')->withStates([$initialState, $state])->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'status_type_id' => $statusType->id,

            // Rows with defined values 30 and 40-50. NewState3 should be 51, as 50 is the highest value
            // from existing states and the ones to be created.
            'value' => [
                'NewState3',
                'NewState1;30',
                'NewState2;40;50',
            ]
        ];

        $definition = (new StoreStateBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusType($statusType->id);

        /* @var State $newState3 */
        $newState3 = $statusType->states->firstWhere('description', '=', 'NewState3');

        $this->assertEquals('51.000', $newState3->min);
        $this->assertEquals('51.000', $newState3->max);

        //
        // Again, but this time the existing states have the highest value.
        //
        // Initial state 100
        $initialState = app(StateBuilder::class)->withMin('100.000')->withMax('100.000')->make();
        $state = app(StateBuilder::class)->withMin('150.000')->withMax('200.000')->make();

        // Two existing states 10 and 20.
        $statusType = app(StatusTypeBuilder::class)->withInitialValue('100.000')->withStates([$initialState, $state])->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'status_type_id' => $statusType->id,

            // Rows with defined values 30 and 40-50. NewState3 should be 201, as 200 is the highest value
            // from existing states and the ones to be created.
            'value' => [
                'NewState3',
                'NewState1;30',
                'NewState2;40;50',
            ]
        ];

        $definition = (new StoreStateBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusType($statusType->id);

        /* @var State $newState3 */
        $newState3 = $statusType->states->firstWhere('description', '=', 'NewState3');

        $this->assertEquals('201.000', $newState3->min);
        $this->assertEquals('201.000', $newState3->max);
    }

}
