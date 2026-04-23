<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreRole;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\RoleBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreRoleTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreRoleTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $role = app(RoleBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = $role->toArray();

        $this->assertCount(0, $definition->roles);
        $definition = (new StoreRole($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->roles);
    }

}
