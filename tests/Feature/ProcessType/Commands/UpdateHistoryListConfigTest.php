<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateHistoryListConfig;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ListConfigBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateHistoryListConfigTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateHistoryListConfigTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_update_history_list_config_set_list_config() {
        $listConfig = app(ListConfigBuilder::class)->withSlug('demo')->make();
        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertNull($definition->history_list_config_slug);

        $definition = (new UpdateHistoryListConfig(['history_list_config_slug' => $listConfig->slug], $definition, $processVersion))->execute();
        $this->assertEquals($listConfig->slug, $definition->history_list_config_slug);
    }

    public function test_commands_update_history_list_config_clear_list_config() {
        $listConfig = app(ListConfigBuilder::class)->withSlug('demo')->make();
        $definition = app(DefinitionBuilder::class)
            ->withListConfigs([$listConfig])
            ->make(['history_list_config_slug' => $listConfig->slug]);
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals($listConfig->slug, $definition->history_list_config_slug);

        $definition = (new UpdateHistoryListConfig(['history_list_config_slug' => null], $definition, $processVersion))->execute();
        $this->assertNull($definition->history_list_config_slug);
    }
}
