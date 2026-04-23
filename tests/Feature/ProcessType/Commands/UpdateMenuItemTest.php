<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateMenuItem;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\MenuItemBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateMenuItemTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateMenuItemTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $menuItem = app(MenuItemBuilder::class)->make(['label' => 'foobar']);
        $definition = app(DefinitionBuilder::class)->withMenuItems([$menuItem])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $definition->menuItems->first()->label);

        $updatedMenuItem = array_merge($definition->menuItems->first()->toArray(), ['label' => 'new foobar']);

        $definition = (new UpdateMenuItem($updatedMenuItem, $definition, $processVersion))->execute();

        $this->assertEquals('new foobar', $definition->menuItems->first()->label);
    }

}
