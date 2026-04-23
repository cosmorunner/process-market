<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreMenuItem;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\MenuItemBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreMenuItemTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreMenuItemTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $menuItem = app(MenuItemBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = $menuItem->toArray();

        $this->assertCount(0, $definition->menuItems);
        $definition = (new StoreMenuItem($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->menuItems);
    }

}
