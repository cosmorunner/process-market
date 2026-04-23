<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\Api;

use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ProcessVersionsControllerTest
 * @package Tests\Feature\Api
 */
class IndexControllerTest extends TestCase {

    use RefreshDatabase;

    public function test_index_controller_user_can_update_context_to_belonging_organisation() {
        /* @var Organisation $organisation */
        $user = $this->login();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();

        // Currently on own profile context
        $this->assertNull($user->contextModel);

        // User is redirected to organisation processes overview after switching contexts.
        $this->patch(route('update_user_context'), ['context' => $organisation->id])
            ->assertRedirect(route('organisation.processes', $organisation));

        // Refresh model to load fresh attributes.
        $user->refresh();
        $this->assertInstanceOf(Organisation::class, $user->contextModel);
    }

    public function test_index_controller_user_cannot_update_context_to_invalid_organisation() {
        /* @var Organisation $organisation */
        $user = $this->login();

        // User is not part of organisation
        $organisation = Organisation::factory()->create();

        // Currently on own profile context
        $this->assertNull($user->contextModel);

        // User is redirected to index page, session has "context" error.
        $this->patch(route('update_user_context'), ['context' => $organisation->id])
            ->assertRedirect(route('index'))
            ->assertSessionHasErrors(['context']);

        // Refresh model to load fresh attributes.
        $user->refresh();

        // Still null
        $this->assertNull($user->contextModel);
    }

    public function test_index_controller_user_can_update_context_to_own_profile() {
        /* @var Organisation $organisation */
        $user = $this->login();
        $organisation = Organisation::factory()->withDefaultRoles()->withAdministrator($user)->create();

        // Manually set context to organisation
        $user->update(['context' => $organisation->id]);

        // Currently on organisation context
        $this->assertInstanceOf(Organisation::class, $user->contextModel);

        // User is redirected to organisation processes overview after switching contexts.
        $this->patch(route('update_user_context'), ['context' => null])->assertRedirect(route('profile.processes'));

        // Refresh model to load fresh attributes.
        $user->refresh();
        $this->assertNull($user->contextModel);
    }

}