<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\Api;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ProcessVersionsControllerTest
 * @package Tests\Feature\Api
 */
class OrganisationControllerTest extends TestCase {

    use RefreshDatabase;

    public function test_organisation_controller_owner_can_delete_organisation() {
        $user = $this->login();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();

        $this->assertCount(1, $user->organisationAccesses);

        $this->delete(route('organisation.delete', $organisation), ['accept' => 'on'])
            ->assertRedirect(route('profile.processes'));

        // Refresh attributes and loaded relations of user.
        $user->refresh();
        $this->assertEmpty($user->organisationAccesses);
        $this->assertEmpty($organisation->members());
    }


    public function test_organisation_controller_user_context_is_set_to_null_when_organisation_is_deleted() {
        $user = $this->login();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();
        $user->update(['context' => $organisation->id]);

        $this->delete(route('organisation.delete', $organisation), ['accept' => 'on'])
            ->assertRedirect(route('profile.processes'));

        // Refresh attributes and loaded relations of user.
        $user->refresh();
        $this->assertNull($user->contextModel);
    }

    public function test_organisation_controller_user_context_is_set_to_null_when_organisation_is_left_while_in_organisation_context() {
        /* @var Organisation $organisation */
        $user = $this->login();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator(User::factory()->create())->create();
        $organisation->addUser($user, $organisation->adminRole());

        $this->assertEquals(null, $user->context);
        $this->patch(route('update_user_context'), ['context' => $organisation->id])
            ->assertRedirectToRoute('organisation.processes', ['organisation' => $organisation->namespace]);
        $this->assertEquals($organisation->id, $user->context);
        $this->post(route('organisation.exit', ['organisation' => $organisation]), ['accept' => 'on'])
            ->assertRedirectToRoute('profile.processes');
        $user->refresh();
        $this->assertEquals(null, $user->context);
    }
}