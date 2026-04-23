<?php

namespace Tests\Unit\Models;

use App\Models\Access;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Role;
use App\Models\Simulation;
use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class UserTest
 * @package Tests\Unit\Models
 */
class UserTest extends TestCase {

    use RefreshDatabase;

    public function test_a_user_has_an_email() {
        $user = User::factory()->create();
        $this->assertIsString($user->email);
    }

    public function test_a_user_has_a_namespace() {
        $user = User::factory()->create();
        $this->assertIsString($user->namespace);
    }

    public function test_a_user_has_a_bio() {
        $user = User::factory()->create();
        $this->assertIsString($user->bio);
    }

    public function test_a_user_has_a_context() {
        $user = User::factory()->create(['context' => Str::uuid()->toString()]);
        $this->assertIsString($user->context);
    }

    public function test_a_user_has_a_context_model() {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->create(['context' => $organisation->id]);
        $this->assertInstanceOf(Organisation::class, $user->contextModel);
    }

    public function test_a_user_has_a_public_path() {
        $user = User::factory()->create();
        $this->assertIsString($user->publicPath());
    }

    public function test_a_user_has_a_profile_processes_path() {
        $user = User::factory()->create();
        $this->assertIsString($user->profileProcessesPath());
    }

    public function test_a_user_has_processes() {
        $user = User::factory()->create();
        Process::factory()->ofCreatorAndAuthor($user)->create();

        $this->assertInstanceOf(Collection::class, $user->processes);
        $this->assertTrue($user->processes->isNotEmpty());
    }

    public function test_a_user_can_have_organisations() {
        $user = User::factory()->create();
        Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();

        $this->assertInstanceOf(Collection::class, $user->organisations);
        $this->assertTrue($user->organisations->isNotEmpty());
    }

    public function test_a_user_can_have_systems() {
        $user = User::factory()->create();
        System::factory()->ofOwner($user)->create();

        $this->assertInstanceOf(Collection::class, $user->systems);
        $this->assertTrue($user->systems->isNotEmpty());
    }

    public function test_a_user_can_have_running_simulation_of_a_graph() {
        $user = $this->login();
        $processVersion = ProcessVersion::factory()->withProcess()->create();

        $this->assertNull($user->runningUserSimulations($processVersion));
        Simulation::factory()->ofUser($user)->ofProcessVersion($processVersion)->create();

        $this->assertInstanceOf(Simulation::class, $user->runningUserSimulations($processVersion));
    }

    public function test_a_user_can_have_running_simulations() {
        $user = $this->login();
        $processVersion = ProcessVersion::factory()->withProcess()->create();

        $this->assertEmpty($user->runningSimulations()->get());
        Simulation::factory()->ofUser($user)->ofProcessVersion($processVersion)->create();

        $this->assertNotEmpty($user->runningSimulations());
    }

    public function test_a_user_can_have_access_to_an_organisation() {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();

        $this->assertTrue($user->hasAccessTo($organisation));
        $this->assertInstanceOf(Access::class, $user->accessTo($organisation));
    }

    public function test_a_user_can_have_a_role_within_an_organisation() {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();

        $this->assertInstanceOf(Role::class, $user->roleWithin($organisation));
    }

    public function test_a_user_can_own_a_process() {
        $user = User::factory()->create();
        $process = Process::factory()->ofCreatorAndAuthor($user)->create();
        $this->assertTrue($user->authored($process));
    }

    public function test_a_user_can_have_a_city() {
        $user = User::factory()->create(['city' => null]);
        $this->assertNull($user->city);

        $user = User::factory()->create(['city' => 'Hamburg']);
        $this->assertEquals('Hamburg', $user->city);
    }

    public function test_a_user_has_an_identicon() {
        $user = User::factory()->create();
        $this->assertIsString($user->image);
        $this->assertNotEmpty($user->image);
    }
}
