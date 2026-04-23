<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreStatusTypeBulk;
use App\ProcessType\Definition;
use App\ProcessType\StatusType;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreStatusTypeBulkTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreStatusTypeBulkTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_store_statustype_bulk_rule_only_name() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'value' => ['NewStatusType']
        ];

        $this->updateDefinition($processVersion, 'StoreStatusTypeBulk', $payload)->assertOk();
    }

    public function test_commands_store_statustype_bulk_rule_with_default_value() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'value' => ['NewStatusType;5']
        ];

        $this->updateDefinition($processVersion, 'StoreStatusTypeBulk', $payload)->assertOk();
    }

    public function test_commands_store_statustype_bulk_rule_with_invalid_default_value() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            // abc is not a valid default value.
            'value' => ['NewStatusType;abc']
        ];

        // Check that validation for 0-index row exists. The decode the HTTP json response body to array.
        $responseArray = $this->updateDefinition($processVersion, 'StoreStatusTypeBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "default" error key exists in the 0-index row.
        $this->assertArrayHasKey('default', $responseArray['errors']['value.0']);
    }

    public function test_commands_store_statustype_bulk_no_default_value() {
        /* @var Definition $definition */
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'value' => ['NewStatusType1']
        ];

        $definition = (new StoreStatusTypeBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusTypes->first();
        $this->assertInstanceOf(StatusType::class, $statusType);
        $this->assertEquals('-1.000', $statusType->default);
        $this->assertEquals('NewStatusType1', $statusType->name);
    }

    public function test_commands_store_statustype_bulk_with_default_value() {
        /* @var Definition $definition */
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'value' => ['NewStatusType1;5']
        ];

        $definition = (new StoreStatusTypeBulk($payload, $definition, $processVersion))->execute();
        $statusType = $definition->statusTypes->first();
        $this->assertInstanceOf(StatusType::class, $statusType);
        $this->assertEquals('5.000', $statusType->default);
        $this->assertEquals('NewStatusType1', $statusType->name);
    }
}
