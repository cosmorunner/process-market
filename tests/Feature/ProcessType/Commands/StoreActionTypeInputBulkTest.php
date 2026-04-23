<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreActionTypeInputBulk;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\CategoryBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreActionTypeInputBulkTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreActionTypeInputBulkTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_action_type_input_bulk_rule_valid_name() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $category = app(CategoryBuilder::class)->ofSystemType()->make();
        $actionType = app(ActionTypeBuilder::class)->ofCategory($category)->make();
        $definition = app(DefinitionBuilder::class)->withCategories([$category])->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'value' => ['new_input_1']
        ];

        $this->updateDefinition($processVersion, 'StoreActionTypeInputBulk', $payload)->assertOk();
    }

    public function test_commands_action_type_input_bulk_rule_invalid_name() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            // Must be lowercase, only a-z, 0-9 and "_".
            'value' => ['New Name']
        ];

        $responseArray = $this->updateDefinition($processVersion, 'StoreActionTypeInputBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "default" error key exists in the 0-index row.
        $this->assertArrayHasKey('name', $responseArray['errors']['value.0']);
    }

    public function test_commands_action_type_input_bulk_rule_invalidate_duplicate_names() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'value' => [
                'new_input_1',
                'new_input_1;test'
            ]
        ];

        $this->updateDefinition($processVersion, 'StoreActionTypeInputBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value');
    }

    public function test_commands_action_type_input_bulk() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'value' => [
                'action_type_input1',
                'action_type_input2;',
                'action_type_input3;Standardwert',
                '=action_type_input4;',
                '~action_type_input5',
                'action_type_input6;#',
            ],
        ];

        $this->assertCount(0, $definition->actionTypes->first()->inputs);
        $definition = (new StoreActionTypeInputBulk($payload, $definition, $processVersion))->execute();
        $this->assertCount(6, $definition->actionTypes->first()->inputs);

        $inputs = collect([
            'action_type_input1' => ['expectedType' => 'basic', 'expectedValue' => null],
            'action_type_input2' => ['expectedType' => 'basic', 'expectedValue' => null],
            'action_type_input3' => ['expectedType' => 'basic', 'expectedValue' => 'Standardwert'],
            'action_type_input4' => ['expectedType' => 'array', 'expectedValue' => null],
            'action_type_input5' => ['expectedType' => 'object', 'expectedValue' => null],
            'action_type_input6' => ['expectedType' => 'auto', 'expectedValueContains' => '[[process.outputs.action_type_input6'],
        ]);

        $inputs->each(function ($config, $name) use ($definition) {
            $actionTypeInput = $definition->actionTypes->first()->inputs->firstWhere('name', '=', $name);

            $this->assertEquals($config['expectedType'], $actionTypeInput->type);

            if (isset($config['expectedValue'])) {
                $this->assertEquals($config['expectedValue'], $actionTypeInput->value);
            }
            else if (isset($config['expectedValueContains'])) {
                $this->assertStringContainsString($config['expectedValueContains'], $actionTypeInput->value);
            }
            else {
                $this->assertEmpty($actionTypeInput->value);
            }
        });
    }
}
