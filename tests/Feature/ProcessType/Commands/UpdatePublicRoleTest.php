<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdatePublicRole;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\RoleBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdatePublicRoleTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdatePublicRoleTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_update_public_role_set_role() {
        $role = app(RoleBuilder::class)->make(['name' => 'foobar']);
        $definition = app(DefinitionBuilder::class)->withRoles([$role])->make(['public_role_id' => null]);
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $definition->roles->first()->name);
        $this->assertNull($definition->public_role_id);

        $definition = (new UpdatePublicRole(['public_role_id' => $role->id], $definition, $processVersion))->execute();
        $this->assertEquals($role->id, $definition->public_role_id);
    }

    public function test_commands_update_public_role_clear_role() {
        $role = app(RoleBuilder::class)->make(['name' => 'foobar']);
        $definition = app(DefinitionBuilder::class)->withRoles([$role])->make(['public_role_id' => $role->id]);
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $definition->roles->first()->name);
        $this->assertEquals($role->id, $definition->public_role_id);

        $definition = (new UpdatePublicRole(['public_role_id' => null], $definition, $processVersion))->execute();
        $this->assertNull($definition->public_role_id);
    }
}
