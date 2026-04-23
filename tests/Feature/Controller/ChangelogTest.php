<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ChangelogTest
 * @package Tests\Feature\Routes
 */
class ChangelogTest extends TestCase {
    use RefreshDatabase;

    public function test_changelog_routes_authenticates_user_can_see_changelog() {
        $this->login();
        $this->get(route('changelog'))->assertOk()->assertSee('1.10.0');
    }

    public function test_changelog_routes_unauthenticates_user_cannot_see_changelog() {
        $this->get(route('changelog'))->assertRedirect();
    }
}