<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateRole;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\RoleBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateRoleTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateRoleTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $role = app(RoleBuilder::class)->make(['name' => 'foobar']);
        $definition = app(DefinitionBuilder::class)->withRoles([$role])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $definition->roles->first()->name);

        $updatedRole = array_merge($definition->roles->first()->toArray(), ['name' => 'new foobar']);

        $definition = (new UpdateRole($updatedRole, $definition, $processVersion))->execute();

        $this->assertEquals('new foobar', $definition->roles->first()->name);
    }

}
