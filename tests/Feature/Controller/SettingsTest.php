<?php

namespace Tests\Feature\Controller;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Class SettingsTest
 * @package Tests\Feature\Routes
 */
class SettingsTest extends TestCase {

    use RefreshDatabase;

    public function test_an_unauthenticated_user_cannot_access_profile_settings() {
        $this->get(route('settings'))->assertRedirect(route('login'));
        $this->get(route('settings.data'))->assertRedirect(route('login'));
        $this->get(route('settings.organisations'))->assertRedirect(route('login'));
        $this->get(route('settings.systems'))->assertRedirect(route('login'));
        $this->get(route('settings.security'))->assertRedirect(route('login'));
        $this->get(route('settings.account'))->assertRedirect(route('login'));
    }

    public function test_an_unauthenticated_user_cannot_update_profile() {
        $this->patch(route('settings.update_password'))->assertRedirect(route('login'));
        $this->patch(route('settings.update_data'))->assertRedirect(route('login'));
    }

    public function test_an_authenticated_user_can_access_profile_settings() {
        $this->login();
        $this->get(route('settings'))->assertRedirect(route('settings.data'));
        $this->get(route('settings.data'))->assertOk();
        $this->get(route('settings.organisations'))->assertOk();
        $this->get(route('settings.systems'))->assertOk();
        $this->get(route('settings.security'))->assertOk();
        $this->get(route('settings.account'))->assertOk();
    }

    public function test_an_authenticated_user_can_update_the_password() {
        $current = 'password';
        $new = '123123123';

        $user = User::factory()->create([
            'password' => Hash::make($current)
        ]);

        $this->login($user);

        // Falsches aktuelles Passwort
        $this->patch(route('settings.update_password'), [
            'current_password' => 'dummy',
            'password' => $new,
            'password_confirmation' => $new
        ])->assertSessionHasErrors('current_password');

        // Falsche Wiederholung
        $this->patch(route('settings.update_password'), [
            'current_password' => $current,
            'password' => $new,
            'password_confirmation' => 'dummy'
        ])->assertSessionHasErrors('password_confirmation');

        // Korrekte Eingabe
        $this->patch(route('settings.update_password'), [
            'current_password' => $current,
            'password' => $new,
            'password_confirmation' => $new
        ])->assertSessionDoesntHaveErrors()->assertRedirect(route('settings.security'));

        $this->assertTrue(Hash::check($new, $user->password));
    }

    public function test_a_user_can_update_their_data() {
        $user = $this->login();

        $this->patch(route('settings.update_data'), [
            'email' => '',
        ])->assertSessionHasErrors('email');

        $this->patch(route('settings.update_data'), [
            'email' => '',
        ])->assertSessionHasErrors('email');

        $this->patch(route('settings.update_data'), [
            'email' => 'bob@web.de',
            'bio' => 'foobar'
        ])->assertRedirect(route('settings.data'));

        $user->refresh();

        $this->assertEquals('bob@web.de', $user->email);
        $this->assertEquals('foobar', $user->bio);
    }

}
