<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\ActionType;
use App\ProcessType\Commands\StoreActionTypeBulk;
use Database\Builder\Definition\CategoryBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreActionTypeBulkTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreActionTypeBulkTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_store_statustype_bulk_rule_with_valid_name() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $category = app(CategoryBuilder::class)->ofSystemType()->make();
        $definition = app(DefinitionBuilder::class)->withCategories([$category])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'value' => ['NewActionType']
        ];

        $this->updateDefinition($processVersion, 'StoreActionTypeBulk', $payload)->assertOk();
    }

    public function test_commands_store_statustype_bulk_rule_with_missing_name() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $category = app(CategoryBuilder::class)->ofSystemType()->make();
        $definition = app(DefinitionBuilder::class)->withCategories([$category])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'value' => ['']
        ];

        $responseArray = $this->updateDefinition($processVersion, 'StoreActionTypeBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "default" error key exists in the 0-index row.
        $this->assertArrayHasKey('name', $responseArray['errors']['value.0']);
    }

    public function test_commands_store_action_type_bulk_simple() {
        $category = app(CategoryBuilder::class)->ofSystemType()->make();
        $definition = app(DefinitionBuilder::class)->withCategories([$category])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'value' => ['NewActiontype1']
        ];

        $definition = (new StoreActionTypeBulk($payload, $definition, $processVersion))->execute();
        $actionType = $definition->actionTypes->first();
        $this->assertInstanceOf(ActionType::class, $actionType);
        $this->assertEquals('NewActiontype1', $actionType->name);
    }
}