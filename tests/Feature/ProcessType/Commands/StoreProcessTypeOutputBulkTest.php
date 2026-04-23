<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreProcessTypeOutputBulk;
use App\ProcessType\Output;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreProcessTypeOutputBulkTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreProcessTypeOutputBulkTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_process_type_output_bulk_rule_valid_name() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            'value' => ['new_output_1']
        ];

        $this->updateDefinition($processVersion, 'StoreProcessTypeOutputBulk', $payload)->assertOk();
    }

    public function test_commands_process_type_output_bulk_rule_invalid_name() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        $payload = [
            // Must be lowercase, only a-z, 0-9 and "_".
            'value' => ['New Name']
        ];

        $responseArray = $this->updateDefinition($processVersion, 'StoreProcessTypeOutputBulk', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value.0')
            ->decodeResponseJson()
            ->json();

        // We check if the "default" error key exists in the 0-index row.
        $this->assertArrayHasKey('name', $responseArray['errors']['value.0']);
    }

    public function test_commands_process_type_output_bulk() {
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'value' => [
                'processtypeoutput1',
                'processtypeoutput2;',
                'processtypeoutput3;Standardwert',
                '=processtypeoutput4;',
                '~processtypeoutput5;'
            ]
        ];

        $this->assertCount(0, $definition->outputs);
        $definition = (new StoreProcessTypeOutputBulk($payload, $definition, $processVersion))->execute();
        $this->assertCount(5, $definition->outputs);

        /* @var Output $output */
        $output = $definition->outputByName('processtypeoutput1');
        $this->assertEmpty($output->default);
        $this->assertEquals('basic', $output->type);

        $output = $definition->outputByName('processtypeoutput2');
        $this->assertEmpty($output->default);
        $this->assertEquals('basic', $output->type);

        $output = $definition->outputByName('processtypeoutput3');
        $this->assertEquals('Standardwert', $output->default);
        $this->assertEquals('basic', $output->type);

        $output = $definition->outputByName('processtypeoutput4');
        $this->assertEmpty($output->default);
        $this->assertEquals('array', $output->type);

        $output = $definition->outputByName('processtypeoutput5');
        $this->assertEmpty($output->default);
        $this->assertEquals('object', $output->type);
    }
}
