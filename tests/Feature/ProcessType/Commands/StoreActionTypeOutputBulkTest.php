<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\Commands\StoreActionTypeOutputBulk;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreActionTypeOutputBulkTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreActionTypeOutputBulkTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_action_type_output_bulk_rule_valid_name() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'value' => ['new_output_1']
        ];

        $this->updateDefinition($processVersion, 'StoreActionTypeOutputBulk', $payload)->assertOk();
    }

    public function test_commands_action_type_output_bulk_rule_invalid_name() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            // Must be lowercase, only a-z, 0-9 and "_".
            'value' => ['New Name']
        ];

        $responseArray = $this->updateDefinition($processVersion, 'StoreActionTypeOutputBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "default" error key exists in the 0-index row.
        $this->assertArrayHasKey('name', $responseArray['errors']['value.0']);
    }

    public function test_commands_action_type_output_bulk_rule_invalidate_duplicate_names() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'value' => [
                'new_output_1',
                'new_output_1;test'
            ]
        ];

        $this->updateDefinition($processVersion, 'StoreActionTypeOutputBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value');
    }

    public function test_commands_action_type_output_bulk_rule_valid_include_in_process_data() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'value' => ['action_type_input_1;;1']
        ];

        $this->updateDefinition($processVersion, 'StoreActionTypeOutputBulk', $payload)->assertOk();
    }

    public function test_commands_action_type_output_bulk_rule_invalid_include_in_process_data() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            // 2-index must be 0 or 1
            'value' => ['action_type_input_1;;a']
        ];

        $responseArray = $this->updateDefinition($processVersion, 'StoreActionTypeOutputBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "default" error key exists in the 0-index row.
        $this->assertArrayHasKey('include_in_process_data', $responseArray['errors']['value.0']);
    }

    public function test_commands_action_type_output_bulk_rule_valid_include_in_input_data() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'value' => ['action_type_input_1;;;1']
        ];

        $this->updateDefinition($processVersion, 'StoreActionTypeOutputBulk', $payload)->assertOk();
    }

    public function test_commands_action_type_output_bulk_rule_invalid_include_in_input_data() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            // 3-index must be 0 or 1
            'value' => ['action_type_input_1;;;a']
        ];

        $responseArray = $this->updateDefinition($processVersion, 'StoreActionTypeOutputBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "default" error key exists in the 0-index row.
        $this->assertArrayHasKey('include_in_input_data', $responseArray['errors']['value.0']);
    }

    public function test_commands_action_type_output_bulk_rule_valid_load_process_data_field() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'value' => ['action_type_input_1;;;;1']
        ];

        $this->updateDefinition($processVersion, 'StoreActionTypeOutputBulk', $payload)->assertOk();
    }

    public function test_commands_action_type_output_bulk_rule_invalid_load_process_data_field() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            // 4-index must be 0 or 1
            'value' => ['action_type_input_1;;;;a']
        ];

        $responseArray = $this->updateDefinition($processVersion, 'StoreActionTypeOutputBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "default" error key exists in the 0-index row.
        $this->assertArrayHasKey('load_process_data_field', $responseArray['errors']['value.0']);
    }

    public function test_commands_action_type_output_bulk_rule_valid_create_form_field() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'value' => ['action_type_input_1;;;;;1']
        ];

        $this->updateDefinition($processVersion, 'StoreActionTypeOutputBulk', $payload)->assertOk();
    }

    public function test_commands_action_type_output_bulk_rule_invalid_create_form_field() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            // 5-index must be 0 or 1
            'value' => ['action_type_input_1;;;;;a']
        ];

        $responseArray = $this->updateDefinition($processVersion, 'StoreActionTypeOutputBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "default" error key exists in the 0-index row.
        $this->assertArrayHasKey('create_form_field', $responseArray['errors']['value.0']);
    }

    public function test_commands_process_type_output_bulk() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'value' => [
                'actiontypeoutput1',
                'actiontypeoutput2!;0;1;0;1',
                'actiontypeoutput3;Standardwert',
                'actiontypeoutput4!;Standardwert;1;1;1;1',
                '=actiontypeoutput5',
                '=actiontypeoutput6!;1;0;0;1',
                '~actiontypeoutput7',
                '~actiontypeoutput8!;1;1;1;0',
            ]
        ];

        $this->assertCount(0, $definition->actionTypes->first()->outputs);
        $definition = (new StoreActionTypeOutputBulk($payload, $definition, $processVersion))->execute();
        $this->assertCount(8, $definition->actionTypes->first()->outputs);

        /* @var ActionType $actionType */
        $actionType = $definition->actionTypes->first();

        $actionTypeOutput = $actionType->output('actiontypeoutput1');
        $this->assertEmpty($actionTypeOutput->default);
        $this->assertEmpty($actionTypeOutput->validation);
        $this->assertEquals('basic', $actionTypeOutput->type);

        $actionTypeOutput = $actionType->output('actiontypeoutput2');
        $this->assertEmpty($actionTypeOutput->default);
        $this->assertContains('required', $actionTypeOutput->validation);
        $this->assertEquals('basic', $actionTypeOutput->type);

        $actionTypeOutput = $actionType->output('actiontypeoutput3');
        $this->assertEquals('Standardwert', $actionTypeOutput->default);
        $this->assertEmpty($actionTypeOutput->validation);
        $this->assertEquals('basic', $actionTypeOutput->type);

        $actionTypeOutput = $actionType->output('actiontypeoutput4');
        $this->assertEquals('Standardwert', $actionTypeOutput->default);
        $this->assertContains('required', $actionTypeOutput->validation);
        $this->assertEquals('basic', $actionTypeOutput->type);

        $actionTypeOutput = $actionType->output('actiontypeoutput5');
        $this->assertEmpty($actionTypeOutput->default);
        $this->assertEmpty($actionTypeOutput->validation);
        $this->assertEquals('array', $actionTypeOutput->type);

        $actionTypeOutput = $actionType->output('actiontypeoutput6');
        $this->assertEmpty($actionTypeOutput->default);
        $this->assertContains('required', $actionTypeOutput->validation);
        $this->assertEquals('array', $actionTypeOutput->type);

        $actionTypeOutput = $actionType->output('actiontypeoutput7');
        $this->assertEmpty($actionTypeOutput->default);
        $this->assertEmpty($actionTypeOutput->validation);
        $this->assertEquals('object', $actionTypeOutput->type);

        $actionTypeOutput = $actionType->output('actiontypeoutput8');
        $this->assertEmpty($actionTypeOutput->default);
        $this->assertContains('required', $actionTypeOutput->validation);
        $this->assertEquals('object', $actionTypeOutput->type);
    }

}
