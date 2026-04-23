<?php

namespace Tests\Unit\Models;

use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class RoleTest
 * @package Tests\Unit\Models
 */
class RoleTest extends TestCase {

    use RefreshDatabase;

    public function test_role_has_an_id() {
        /* @var Role $role */
        $role = Role::factory()->create();
        $this->assertIsString($role->id);
    }

    public function test_role_has_a_name() {
        /* @var Role $role */
        $role = Role::factory()->create();
        $this->assertIsString($role->name);
    }

    public function test_role_has_a_description() {
        /* @var Role $role */
        $role = Role::factory()->create();
        $this->assertIsString($role->description);
    }

    public function test_role_has_an_owner_id() {
        /* @var Role $role */
        $role = Role::factory()->create();
        $this->assertIsString($role->owner_id);
    }

    public function test_role_has_an_owner_type() {
        /* @var Role $role */
        $role = Role::factory()->create();
        $this->assertEquals(Organisation::class, $role->owner_type);
    }

    public function test_role_is_admin() {
        /* @var Role $role */
        $role = Role::factory()->create();
        $this->assertFalse($role->is_admin);
    }

    public function test_role_has_create_info() {
        /* @var Role $role */
        $role = Role::factory()->create();
        $this->assertNotNull($role->created_at);
    }

    public function test_role_has_update_info() {
        /* @var Role $role */
        $role = Role::factory()->create();
        $this->assertNotNull($role->updated_at);
    }

    public function test_role_belongs_to_an_owner() {
        /* @var Role $role */
        $role = Role::factory()->withOwner()->create();
        $this->assertInstanceOf(Organisation::class, $role->owner);
    }

    public function test_role_has_permissions() {
        /* @var Role $role */
        $role = Role::factory()->withOwner()->withPermissions()->create();
        $this->assertNotEmpty($role->permissions);
    }

    public function test_role_has_accesses() {
        /* @var Role $role */
        /* @var User $user */

        $role = Role::factory()->ofAdministratorType()->withOwner()->create();
        $user = User::factory()->create();

        $role->owner->addUser($user, $role);

        $this->assertInstanceOf(Collection::class, $role->accesses);
        $this->assertNotEmpty($role->accesses);
    }

    public function test_role_can_add_permissions() {
        /* @var Role $role */
        /* @var Permission $permission */
        $permission = Permission::factory()->create();
        $role = Role::factory()->create();
        $role->addPermission($permission);
        $this->assertNotEmpty($role->permissions->where('id', $permission->id)->first());
    }

    public function test_role_can_remove_permissions() {
        /* @var Role $role */
        /* @var Permission $permission */
        $permission = Permission::factory()->create();
        $role = Role::factory()->create();
        $role->addPermission($permission);
        $this->assertNotEmpty($role->permissions->where('id', $permission->id));
        $role->removePermission($permission);
        $role->refresh();
        $this->assertEmpty($role->permissions->where('id', $permission->id));
    }

    public function test_role_can_check_whether_it_has_a_certain_right() {
        /* @var Permission $permission */
        /* @var Role $role */
        $role = Role::factory()->ofAdministratorType()->create();
        $permission = $role->permissions->first();

        $this->assertTrue($role->can($permission->ident));
    }

    public function test_role_can_check_whether_it_has_one_of_many_rights() {
        /* @var Permission $permission */
        /* @var Role $role */
        $role = Role::factory()->ofAdministratorType()->create();
        $permission = $role->permissions->first();

        $this->assertTrue($role->canAny(collect([$permission->ident, 'foo-bar'])));
    }

    public function test_role_can_be_an_admin_role() {
        /* @var Role $role */
        $role = Role::factory()->create();
        $this->assertFalse($role->isAdmin());
        $role = Role::factory()->ofAdministratorType()->create();
        $this->assertTrue($role->isAdmin());
    }
}