<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteMenuItem;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\MenuItemBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteMenuItemTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteMenuItemTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $menuItem = app(MenuItemBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withMenuItems([$menuItem])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'id' => $menuItem->id,
        ];

        $this->assertCount(1, $definition->menuItems);
        $definition = (new DeleteMenuItem($payload, $definition, $processVersion))->execute();
        $this->assertCount(0, $definition->menuItems);
    }

}
