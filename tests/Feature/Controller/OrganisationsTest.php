<?php

namespace Tests\Feature\Controller;

use App\Enums\Visibility;
use App\Models\Invitation;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class OrganisationsTest
 * @package Tests\Feature\Routes
 */
class OrganisationsTest extends TestCase {

    use RefreshDatabase;

    public function test_organisations_routes_user_can_sort_processes() {
        /* @var Organisation $organisation */
        $user = $this->login();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();

        $processVersionZ = ProcessVersion::factory()->create(['complexity_score' => 2]);
        $processVersionA = ProcessVersion::factory()->create(['complexity_score' => 1]);
        $processVersionM = ProcessVersion::factory()->create(['complexity_score' => 3]);

        Process::factory()
            ->withLatestVersion($processVersionZ)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'Z Process'])
            ->setUpdatedAt(now()->subDays(3))
            ->save();
        Process::factory()
            ->withLatestVersion($processVersionA)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'A Process'])
            ->setUpdatedAt(now()->subDays())
            ->save();
        Process::factory()
            ->withLatestVersion($processVersionM)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'M Process'])
            ->setUpdatedAt(now()->subDays(2))
            ->save();

        // Sort for 'lastChange_desc'
        $response = $this->get(route('organisation.processes', ['organisation' => $organisation, 'sort' => 'lastChange_desc']))
            ->assertOk();
        $this->assertEquals('A Process', $response->viewData('processes')->first()->title);

        // Sort for 'alphabetically_desc'
        $response = $this->get(route('organisation.processes', [
            'organisation' => $organisation,
            'sort' => 'alphabetically_desc'
        ]))->assertOk();
        $this->assertEquals('Z Process', $response->viewData('processes')->first()->title);

        // Sort for 'complexity_desc'
        $response = $this->get(route('organisation.processes', ['organisation' => $organisation, 'sort' => 'complexity_desc']))
            ->assertOk();
        $this->assertEquals('M Process', $response->viewData('processes')->first()->title);
    }

    public function test_organisations_controller_user_can_sort_public_processes() {
        /* @var Organisation $organisation */
        $user = $this->login();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();

        $processVersionZ = ProcessVersion::factory()->create(['complexity_score' => 2]);
        $processVersionA = ProcessVersion::factory()->create(['complexity_score' => 1]);
        $processVersionM = ProcessVersion::factory()->create(['complexity_score' => 3]);

        Process::factory()
            ->withLatestPublishedVersion($processVersionZ)
            ->withVisibility(Visibility::Public->value)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'Z Process'])
            ->setUpdatedAt(now()->subDays(3))
            ->save();
        Process::factory()
            ->withLatestPublishedVersion($processVersionA)
            ->withVisibility(Visibility::Public->value)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'A Process'])
            ->setUpdatedAt(now()->subDays())
            ->save();
        Process::factory()
            ->withLatestPublishedVersion($processVersionM)
            ->withVisibility(Visibility::Public->value)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'M Process'])
            ->setUpdatedAt(now()->subDays(2))
            ->save();

        // Sort for 'lastChange_desc'
        $response = $this->get(route('organisation.show', ['organisation' => $organisation, 'sort' => 'lastChange_desc']))
            ->assertOk();
        $this->assertEquals('A Process', $response->viewData('processes')->first()->title);

        // Sort for 'alphabetically_desc'
        $response = $this->get(route('organisation.show', ['organisation' => $organisation, 'sort' => 'alphabetically_desc']))
            ->assertOk();
        $this->assertEquals('Z Process', $response->viewData('processes')->first()->title);

        // Sort for 'complexity_desc'
        $response = $this->get(route('organisation.show', ['organisation' => $organisation, 'sort' => 'complexity_desc']))
            ->assertOk();
        $this->assertEquals('M Process', $response->viewData('processes')->first()->title);
    }

    public function test_organisations_controller_user_can_search_processes() {
        /* @var Organisation $organisation */
        $user = $this->login();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();

        $processVersionZ = ProcessVersion::factory()->create(['complexity_score' => 2]);
        $processVersionA = ProcessVersion::factory()->create(['complexity_score' => 1]);
        $processVersionM = ProcessVersion::factory()->create(['complexity_score' => 3]);

        Process::factory()
            ->withLatestVersion($processVersionZ)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'Z Process'])
            ->setUpdatedAt(now()->subDays(3))
            ->save();
        Process::factory()
            ->withLatestVersion($processVersionA)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'A Process'])
            ->setUpdatedAt(now()->subDays())
            ->save();
        Process::factory()
            ->withLatestVersion($processVersionM)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'M Process'])
            ->setUpdatedAt(now()->subDays(2))
            ->save();

        // Search for 'Z'
        $response = $this->get(route('organisation.processes', ['organisation' => $organisation, 'search' => 'Z']))->assertOk();
        $this->assertCount(1, $response->viewData('processes'));
        $this->assertEquals('Z Process', $response->viewData('processes')->first()->title);
    }

    public function test_organisations_controller_user_can_search_public_processes() {
        /* @var Organisation $organisation */
        $user = $this->login();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();

        $processVersionZ = ProcessVersion::factory()->create(['complexity_score' => 2]);
        $processVersionA = ProcessVersion::factory()->create(['complexity_score' => 1]);
        $processVersionM = ProcessVersion::factory()->create(['complexity_score' => 3]);

        Process::factory()
            ->withLatestPublishedVersion($processVersionZ)
            ->withVisibility(Visibility::Public->value)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'Z Process'])
            ->setUpdatedAt(now()->subDays(3))
            ->save();
        Process::factory()
            ->withLatestPublishedVersion($processVersionA)
            ->withVisibility(Visibility::Public->value)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'A Process'])
            ->setUpdatedAt(now()->subDays())
            ->save();
        Process::factory()
            ->withLatestPublishedVersion($processVersionM)
            ->withVisibility(Visibility::Public->value)
            ->ofAuthor($organisation)
            ->ofCreator($user)
            ->create(['title' => 'M Process'])
            ->setUpdatedAt(now()->subDays(2))
            ->save();

        // Search for 'Z'
        $response = $this->get(route('organisation.show', ['organisation' => $organisation, 'search' => 'Z']))->assertOk();
        $this->assertCount(1, $response->viewData('processes'));
        $this->assertEquals('Z Process', $response->viewData('processes')->first()->title);
    }

    public function test_organisation_controller_user_has_owner_role_after_creation() {
        $user = $this->login();

        $data = [
            'name' => 'Beispielorganisation',
            'namespace' => 'beispiel-namespace'
        ];

        $this->post(route('organisation.store'), $data);

        $organisation = Organisation::whereNamespace($data['namespace'])->first();
        $this->assertInstanceOf(Organisation::class, $organisation);

        $userRole = $organisation->roleOf($user);
        $this->assertInstanceOf(Role::class, $userRole);
        $this->assertEquals($organisation->ownerRole()->id, $userRole->id);
    }

    public function test_organisation_controller_only_owner_can_transfer_ownership_to_new_user() {
        $owner = $this->login();
        $user = User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'name' => 'Beispielorganisation',
            'namespace' => 'beispiel-namespace'
        ];

        $this->post(route('organisation.store'), $data);
        $organisation = Organisation::whereNamespace($data['namespace'])->first();
        $this->assertInstanceOf(Organisation::class, $organisation);

        $data = [
            'email' => $user->email,
            'role' => $organisation->adminRole()->id
        ];

        $this->post(route('organisation.invitation.store', ['organisation' => $organisation]), $data);
        $invitation = Invitation::whereEmail($user->email)->first();
        $this->assertInstanceOf(Invitation::class, $invitation);
        $this->get(route('invitation.accept', ['invitation' => $invitation]));

        $ownerRole = $organisation->ownerRole();
        $adminRole = $organisation->adminRole();
        $owners = $organisation->membersOfRole($ownerRole);
        $admins = $organisation->membersOfRole($adminRole);

        $this->assertCount(1, $owners);
        $this->assertCount(1, $admins);
        $this->assertEquals($owner->id, $owners->first()->id);
        $this->assertEquals($user->id, $admins->first()->id);

        // Swith logged in user.
        $this->login($user);
        $this->patch(route('organisation.members.transfer-owner', ['organisation' => $organisation, 'user' => $user]));

        $owners = $organisation->membersOfRole($ownerRole);
        $admins = $organisation->membersOfRole($adminRole);

        $this->assertEquals($owner->id, $owners->first()->id);
        $this->assertEquals($user->id, $admins->first()->id);

        // Switch to owner.
        $this->login($owner);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->patch(route('organisation.members.transfer-owner', ['organisation' => $organisation, 'user' => $user]))
            ->assertSessionHasNoErrors();

        $owners = $organisation->membersOfRole($ownerRole);
        $admins = $organisation->membersOfRole($adminRole);
        $this->assertEquals($user->id, $owners->first()->id);
        $this->assertEquals($owner->id, $admins->first()->id);
    }

    public function test_organisation_controller_user_cannot_remove_itself_when_being_owner() {
        $owner = $this->login();
        $user = User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'name' => 'Beispielorganisation',
            'namespace' => 'beispiel-namespace'
        ];

        $this->post(route('organisation.store'), $data);
        $organisation = Organisation::whereNamespace($data['namespace'])->first();
        $this->assertInstanceOf(Organisation::class, $organisation);

        $data = [
            'email' => $user->email,
            'role' => $organisation->adminRole()->id
        ];

        $this->post(route('organisation.invitation.store', ['organisation' => $organisation]), $data);
        $invitation = Invitation::whereEmail($user->email)->first();
        $this->assertInstanceOf(Invitation::class, $invitation);
        $this->get(route('invitation.accept', ['invitation' => $invitation]));

        $this->assertCount(2, $organisation->members());

        // Switch to logged in owner.
        $this->login($owner);
        $this->delete(route('organisation.members.delete', [
            'organisation' => $organisation,
            'user' => $user
        ]), ['accept' => 'on']);

        $this->assertCount(1, $organisation->members());

        $this->delete(route('organisation.members.delete', [
            'organisation' => $organisation,
            'user' => $owner
        ]), ['accept' => 'on']);

        $this->assertCount(1, $organisation->members());
    }

    public function test_organisation_controller_owner_cannot_be_removed() {
        $owner = $this->login();
        $user = User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'name' => 'Beispielorganisation',
            'namespace' => 'beispiel-namespace'
        ];

        $this->post(route('organisation.store'), $data);
        $organisation = Organisation::whereNamespace($data['namespace'])->first();
        $this->assertInstanceOf(Organisation::class, $organisation);

        $data = [
            'email' => $user->email,
            'role' => $organisation->roles()->where('is_admin', '=', true)->firstWhere('is_owner', '=', false)->id
        ];

        $this->post(route('organisation.invitation.store', ['organisation' => $organisation]), $data);
        $invitation = Invitation::whereEmail($user->email)->first();
        $this->assertInstanceOf(Invitation::class, $invitation);
        $this->get(route('invitation.accept', ['invitation' => $invitation]));

        $this->assertCount(2, $organisation->members());

        // Swith to other user
        $this->login($user);

        $this->delete(route('organisation.members.delete', [
            'organisation' => $organisation,
            'user' => $owner
        ]), ['accept' => 'on']);

        $this->assertCount(2, $organisation->members());
    }

    public function test_organisation_controller_cant_add_second_owner() {
        $this->login();
        $user = User::factory()->create(['email' => 'test@example.com']);

        $data = [
            'name' => 'Beispielorganisation',
            'namespace' => 'beispiel-namespace'
        ];

        $this->post(route('organisation.store'), $data);
        $organisation = Organisation::whereNamespace($data['namespace'])->first();
        $this->assertInstanceOf(Organisation::class, $organisation);

        $data = [
            'email' => $user->email,
            'role' => $organisation->ownerRole()->id
        ];

        $this->post(route('organisation.invitation.store', ['organisation' => $organisation]), $data)
            ->assertSessionHas('error', 'Kann keine weiteren Eigentümer einladen');
    }
}