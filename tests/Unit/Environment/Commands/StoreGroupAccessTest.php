<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreGroup;
use App\Environment\Commands\StoreGroupAccess;
use App\Environment\Commands\StoreGroupRole;
use App\Environment\Commands\StoreUser;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class StoreGroupAccessTest
 * @package Tests\Unit\Environment\Commands
 */
class StoreGroupAccessTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_store_group_access() {
        $environment = Environment::factory()->emptyWithName('Standard')->make();

        $environment = (new StoreUser([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'identity_process_type' => 'allisa/person@1.0.0',
            'aliases' => ['alias1'],
            'tags' => ['tag1'],
        ], $environment))->execute();

        $user = $environment->blueprint->users->first();

        $environment = (new StoreGroup([
            'name' => 'Group 1',
            'aliases' => ['alias1'],
            'tags' => ['tag1'],
        ], $environment))->execute();

        $group = $environment->blueprint->groups->first();

        $environment = (new StoreGroupRole([
            'id' => Str::uuid()->toString(),
            'group_id' => $group->id,
            'name' => 'Demo',
            'locked' => true,
        ], $environment))->execute();

        $role = $environment->blueprint->groupRoles;

        // for some reason there is already an entry?!
        $this->assertCount(1, $environment->blueprint->groupAccesses);

        $environment = (new StoreGroupAccess([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'role_id' => $role
        ], $environment))->execute();

        $this->assertCount(2, $environment->blueprint->groupAccesses);
    }
}
