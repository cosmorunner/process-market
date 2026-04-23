<?php

namespace Tests\Unit\Models;

use App\Models\Access;
use App\Models\Invitation;
use App\Models\License;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\ProcessLicense;
use App\Models\Role;
use App\Models\SolutionLicense;
use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

/**
 * Class OrganisationTest
 * @package Tests\Unit\Models
 */
class OrganisationTest extends TestCase {

    use RefreshDatabase;

    public function test_organisation_has_an_id() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertTrue(Uuid::isValid($organisation->id));
    }

    public function test_organisation_has_a_name() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertNotEmpty($organisation->name);
    }

    public function test_organisation_has_a_namespace() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertNotEmpty($organisation->namespace);
    }

    public function test_organisation_has_a_description() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertIsString($organisation->description);
    }

    public function test_organisation_has_an_image() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertNotEmpty($organisation->image);
    }

    public function test_organisation_has_a_city() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertNotEmpty($organisation->city);
    }

    public function test_organisation_has_a_link() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertIsString($organisation->link);
    }

    public function test_organisation_has_create_info() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertNotEmpty($organisation->created_at);
    }

    public function test_organisation_has_update_info() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertNotEmpty($organisation->updated_at);
    }

    public function test_organisation_has_accesses() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->withAccesses()->create();
        $this->assertInstanceOf(Access::class, $organisation->accesses->first());
    }

    public function test_organisation_has_invitations() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->withInvitations()->create();
        $this->assertInstanceOf(Invitation::class, $organisation->invitations->first());
    }

    public function test_organisation_has_processes() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->withProcesses()->create();
        $this->assertInstanceOf(Process::class, $organisation->processes->first());
    }

    public function test_organisation_has_roles() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->withDefaultRoles()->create();
        $this->assertInstanceOf(Role::class, $organisation->roles->first());
        $this->assertCount(5, $organisation->roles);
    }

    public function test_organisation_has_systems() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->withSystems()->create();
        $this->assertInstanceOf(System::class, $organisation->systems->first());
    }

    public function test_organisation_has_licenses() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->withLicenses()->create();
        $this->assertInstanceOf(License::class, $organisation->licenses->first());
    }

    public function test_organisation_has_process_licenses() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->withProcessLicenses()->create();
        $this->assertInstanceOf(ProcessLicense::class, $organisation->processLicenses->first());
    }

    public function test_organisation_has_solution_licenses() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->withSolutionLicenses()->create();
        $this->assertInstanceOf(SolutionLicense::class, $organisation->solutionLicenses->first());
    }

    public function test_organisation_has_an_admin_role() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->withDefaultRoles()->create();
        $this->assertInstanceOf(Role::class, $organisation->adminRole());
        $this->assertCount(5, $organisation->roles);
    }

    public function test_organisation_has_members() {
        /* @var Organisation $organisation */
        /* @var Collection<Access> $accesses */
        $accesses = Access::factory()->count(2)->ofRecipient()->create();
        $organisation = Organisation::factory()->withAccesses($accesses)->create();
        $this->assertCount(2, $organisation->members());
    }

    public function test_organisation_can_identify_the_members_of_a_role() {
        /* @var User $user */
        /* @var Organisation $organisation */
        $user = User::factory()->create();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();
        $adminRole = $organisation->adminRole();
        $organisation->refresh();

        $this->assertNotEmpty($organisation->membersOfRole($adminRole));
    }

    public function test_organisation_can_identify_the_role_of_a_member() {
        /* @var User $user */
        /* @var Organisation $organisation */
        $user = User::factory()->create();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();
        $this->assertInstanceOf(Role::class, $organisation->roleOf($user));
    }

    public function test_organisation_can_check_whether_a_user_is_a_member() {
        /* @var User $user */
        /* @var Organisation $organisation */
        $user = User::factory()->create();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();
        $this->assertTrue($organisation->isMember($user));
    }

    public function test_organisation_has_a_profile_process_path() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertIsString($organisation->profileProcessesPath());
    }

    public function test_organisation_has_a_profile_solution_path() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertIsString($organisation->profileSolutionsPath());
    }

    public function test_organisation_has_a_profile_license_path() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertIsString($organisation->profileLicensesPath());
    }

    public function test_organisation_has_a_public_path() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertIsString($organisation->publicPath());
    }

    public function test_organisation_has_a_settings_path() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertIsString($organisation->settingsPath());
    }

    public function test_organisation_get_namespace() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertIsString($organisation->getRouteKeyName());
    }

    public function test_organisation_resolves_route_bindings() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertInstanceOf(Organisation::class, $organisation->resolveRouteBinding($organisation->namespace));
    }

    public function test_organisation_has_image_path() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertIsString($organisation->imagePath());
    }

    public function test_organisation_has_thumbnail_path() {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $this->assertIsString($organisation->thumbnailPath());
    }
}
