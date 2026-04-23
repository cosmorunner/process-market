<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class PermissionTest
 * @package Tests\Unit\Models
 */
class PermissionTest extends TestCase {

    use RefreshDatabase;

    public function test_permission_has_an_id() {
        /* @var Permission $permission */
        $permission = Permission::factory()->create();
        $this->assertIsString($permission->id);
    }

    public function test_permission_has_a_name() {
        /* @var Permission $permission */
        $permission = Permission::factory()->create();
        $this->assertIsString($permission->name);
    }

    public function test_permission_has_a_description() {
        /* @var Permission $permission */
        $permission = Permission::factory()->create();
        $this->assertIsString($permission->name);
    }

    public function test_permission_has_an_ident() {
        /* @var Permission $permission */
        $permission = Permission::factory()->create();
        $this->assertIsString($permission->ident);
    }

    public function test_permission_is_active() {
        /* @var Permission $permission */
        $permission = Permission::factory()->create();
        $this->assertTrue($permission->active);
    }

    public function test_permission_belongs_to_role() {
        /* @var Permission $permission */
        $permission = Permission::factory()->ofRole()->create();
        $this->assertInstanceOf(Collection::class, $permission->roles);
    }

    public function test_permission_can_be_activated() {
        /* @var Permission $permission */
        $permission = Permission::factory()->deactivated()->create();
        $this->assertFalse($permission->active);
        $permission->activate();
        $this->assertTrue($permission->active);
    }

    public function test_permission_can_be_deactivated() {
        /* @var Permission $permission */
        $permission = Permission::factory()->activated()->create();
        $this->assertTrue($permission->active);
        $permission->deactivate();
        $this->assertFalse($permission->active);
    }

    public function test_permission_can_be_assigned_to_a_role() {
        /* @var Permission $permission */
        /* @var Role $role */
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();
        $this->assertEmpty($permission->roles);
        $permission->addToRole($role);
        $permission->load('roles');
        $this->assertNotEmpty($permission->roles);
    }

    public function test_permission_can_be_removed_from_a_role() {
        /* @var Permission $permission */
        /* @var Role $role */
        $permission = Permission::factory()->ofRole(Role::factory()->create())->create();
        $role = $permission->roles->first();
        $this->assertContains($role, $permission->roles);
        $permission->removeFromRole($role);
        $permission->load('roles');
        $this->assertNotContains($role, $permission->roles);
    }
}