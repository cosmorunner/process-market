<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreGroupRole;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class StoreGroupRoleTest
 * @package Tests\Unit\Environment\Commands
 */
class StoreGroupRoleTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_store_group_role() {
        $environment = Environment::factory()->create();

        $this->assertCount(0, $environment['blueprint']->groupRoles);

        $environment = (new StoreGroupRole([
            'id' => Str::uuid()->toString(),
            'group_id' => Str::uuid()->toString(),
            'name' => 'Demo',
            'locked' => true,
        ], $environment))->execute();

        $this->assertCount(1, $environment['blueprint']->groupRoles);
    }
}
