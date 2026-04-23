<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateListConfig;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ListConfigBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateListConfigTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateListConfigTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $listConfig = app(ListConfigBuilder::class)->make(['slug' => 'foobar']);
        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $definition->listConfigs->first()->slug);

        $updatedListConfig = array_merge($definition->listConfigs->first()->toArray(), ['slug' => 'new foobar']);

        $definition = (new UpdateListConfig($updatedListConfig, $definition, $processVersion))->execute();

        $this->assertEquals('new foobar', $definition->listConfigs->first()->slug);
    }

}
