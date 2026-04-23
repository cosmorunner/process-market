<?php

namespace Tests\Unit\Models;

use App\Models\Access;
use App\Models\Process;
use App\Models\Role;
use App\Models\User;
use App\Traits\UsesAccesses;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class AccessTest
 * @package Tests\Unit\Models
 */
class AccessTest extends TestCase {

    use RefreshDatabase;

    public function test_access_has_an_id() {
        /* @var Access $access */
        $access = Access::factory()->create();
        $this->assertNotEmpty($access->id);
    }

    public function test_access_has_a_role_id() {
        /* @var Access $access */
        $access = Access::factory()->create();
        $this->assertNotEmpty($access->role_id);
    }

    public function test_access_has_a_resource_id() {
        /* @var Access $access */
        $access = Access::factory()->create();
        $this->assertNotEmpty($access->resource_id);
    }

    public function test_access_has_a_resource_type() {
        /* @var Access $access */
        $access = Access::factory()->create();
        $this->assertNotEmpty($access->resource_type);
    }

    public function test_access_has_recipient_id() {
        /* @var Access $access */
        $access = Access::factory()->create();
        $this->assertNotEmpty($access->recipient_id);
    }

    public function test_access_has_create_info() {
        /* @var Access $access */
        $access = Access::factory()->create();
        $this->assertNotEmpty($access->created_at);
    }

    public function test_access_has_a_recipient_type() {
        /* @var Access $access */
        $access = Access::factory()->create();
        $this->assertEquals(User::class, $access->recipient_type);
    }

    public function test_access_belongs_to_a_recipient() {
        /* @var Access $access */
        $access = Access::factory()->ofRecipient()->create();
        $this->assertInstanceOf(User::class, $access->recipient);
    }

    public function test_access_belongs_to_a_resource() {
        /* @var Access $access */
        $access = Access::factory()->ofResource()->create();
        $this->assertInstanceOf(Process::class, $access->resource);
    }

    public function test_access_has_a_role() {
        /* @var Access $access */
        $access = Access::factory()->ofRole()->create();
        $this->assertInstanceOf(Role::class, $access->role);
    }

    public function test_access_grants_access() {
        /* @var Access $access */
        /* @var null|UsesAccesses|Process $resource */
        /* @var User $recipient */
        /* @var Role $role */

        $resource = Process::factory()->create();
        $recipient = User::factory()->create();
        $role = Role::factory()->create();

        $access = Access::grant($resource, $recipient, $role);

        $this->assertEquals($resource->id, $access->resource_id);
        $this->assertEquals(get_class($resource), $access->resource_type);
        $this->assertEquals($recipient->id, $access->recipient_id);
        $this->assertEquals(get_class($recipient), $access->recipient_type);
        $this->assertEquals($role->id, $access->role_id);
    }

    public function test_access_allows_permission() {
        /* @var Access $access */
        /* @var Role $reporterRole */
        /* @var Role $adminRole */

        $reporterRole = Role::factory()->ofReporterType()->create();
        $access = Access::factory()->ofRole($reporterRole)->create();

        $this->assertTrue($access->allows('processes.view'));
        $this->assertFalse($access->allows('processes.create'));

        $adminRole = Role::factory()->ofAdministratorType()->create();
        $access = Access::factory()->ofRole($adminRole)->create();

        $this->assertTrue($access->allows('processes.view'));
        $this->assertTrue($access->allows('processes.create'));
    }
}