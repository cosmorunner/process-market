<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreListConfig;
use App\ProcessType\ListConfig;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ListConfigBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreListConfigTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreListConfigTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $listConfig = app(ListConfigBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = $listConfig->toArray();

        $this->assertCount(0, $definition->listConfigs);
        $definition = (new StoreListConfig($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->listConfigs);
    }

    public function test_commands_can_create_accesses_list() {
        $listConfig = app(ListConfigBuilder::class)->make(config('list_templates.group_members'));
        $definition = app(DefinitionBuilder::class)->make();
        $process = $this->createProcessWithVersionHistoryAndUser($definition);

        $this->assertInstanceOf(Process::class, $process);

        /* @var ProcessVersion $processVersion */
        $processVersion = $process->versions->first();

        $this->assertNotEmpty($processVersion->history_head);

        $this->patch(route('api.process_version.update_definition', $processVersion), [
            'command' => 'StoreListConfig',
            'payload' => $listConfig->toArray()
        ])->assertOk();

        // Refresh model to get new attributes
        $processVersion->refresh();
        $this->assertInstanceOf(ListConfig::class, $processVersion->definition->listConfig($listConfig->slug));
    }
}
