<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\DeleteGroupRoles;
use App\Environment\Commands\StoreGroupRole;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class DeleteGroupRolesTest
 * @package Tests\Unit\Environment\Commands
 */
class DeleteGroupRolesTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_delete_group_role() {
        $environment = Environment::factory()->create();

        $this->assertCount(0, $environment['blueprint']->groupRoles);

        $id = Str::uuid()->toString();
        $groupId = Str::uuid()->toString();
        $environment = (new StoreGroupRole([
            'id' => $id,
            'group_id' => $groupId,
            'name' => 'Demo',
            'locked' => true,
        ], $environment))->execute();

        $this->assertCount(1, $environment['blueprint']->groupRoles);

        $environment = (new DeleteGroupRoles([
            'id' => $id,
            'group_id' => $groupId,
        ], $environment))->execute();

        $this->assertCount(0, $environment['blueprint']->groupRoles);
    }
}
