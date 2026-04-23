<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteListConfig;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ListConfigBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteListConfigTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteListConfigTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_delete_list_config_simple() {
        $listConfig = app(ListConfigBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'id' => $listConfig->id,
        ];

        $this->assertCount(1, $definition->listConfigs);
        $definition = (new DeleteListConfig($payload, $definition, $processVersion))->execute();
        $this->assertCount(0, $definition->listConfigs);
    }

}
