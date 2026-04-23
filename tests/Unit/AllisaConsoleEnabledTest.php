<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class AllisaConsoleEnabledTest
 * @package Tests\Unit
 */
class AllisaConsoleEnabledTest extends TestCase {

    use RefreshDatabase;

    public function test_allisa_console_not_enabled_api_access() {
        Config::set('allisa.console.enabled', true);

        $this->login();
        $this->post(route('api.store_allisa_platform'))->assertUnprocessable();
    }

    public function test_allisa_console_enabled_api_access() {
        Config::set('allisa.console.enabled', false);

        $this->login();
        $this->post(route('api.store_allisa_platform'))->assertNotFound();
    }

    public function test_allisa_console_not_enabled_create_demo_access() {
        Config::set('allisa.console.enabled', true);

        $this->login();
        $this->get(route('profile.create_allisa_demo'))->assertOk();
    }

    public function test_allisa_console_enabled_create_demo_access() {
        Config::set('allisa.console.enabled', false);
        $this->login();
        $this->get(route('profile.create_allisa_demo'))->assertNotFound();
    }

    public function test_allisa_console_enabled_banner_showing() {
        Config::set('allisa.console.enabled', true);

        $this->login();
        $this->get(route('profile.processes'))
            ->assertOk()
            ->assertViewIs('profile.processes')
            ->assertSee('Mit der Allisa Plattform kostenlos starten');
    }

    public function test_allisa_console_not_enabled_banner_showing() {
        Config::set('allisa.console.enabled', false);
        $this->login();

        $this->get(route('profile.processes'))
            ->assertOk()
            ->assertViewIs('profile.processes')
            ->assertDontSee('Mit der Allisa Plattform kostenlos starten');
    }
}