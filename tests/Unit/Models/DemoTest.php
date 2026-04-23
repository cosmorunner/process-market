<?php

namespace Tests\Unit\Models;

use App\Models\Demo;
use App\Models\Organisation;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DemoTest
 * @package Tests\Unit\Models
 */
class DemoTest extends TestCase {

    use RefreshDatabase;

    public function test_demo_has_an_id() {
        /* @var Demo $demo */
        $demo = Demo::factory()->create();
        $this->assertNotEmpty($demo->id);
    }

    public function test_demo_has_a_user_id() {
        /* @var Demo $demo */
        $demo = Demo::factory()->create();
        $this->assertNotEmpty($demo->user_id);
    }

    public function test_demo_has_a_solution_id() {
        /* @var Demo $demo */
        $demo = Demo::factory()->create();
        $this->assertNotEmpty($demo->solution_id);
    }

    public function test_demo_has_a_organisation_id() {
        /* @var Demo $demo */
        $demo = Demo::factory()->create();
        $this->assertNotEmpty($demo->organisation_id);
    }

    public function test_demo_has_a_solution_version_id() {
        /* @var Demo $demo */
        $demo = Demo::factory()->create();
        $this->assertNotEmpty($demo->solution_version_id);
    }

    public function test_demo_has_a_token() {
        /* @var Demo $demo */
        $demo = Demo::factory()->create();
        $this->assertNotEmpty($demo->token);
    }

    public function test_demo_has_an_allisa_user_id() {
        /* @var Demo $demo */
        $demo = Demo::factory()->create();
        $this->assertNotEmpty($demo->allisa_user_id);
    }

    public function test_demo_has_a_user() {
        /* @var Demo $demo */
        $demo = Demo::factory()->withUser()->create();
        $this->assertInstanceOf(User::class, $demo->user);
    }

    public function test_demo_has_a_solution() {
        /* @var Demo $demo */
        $demo = Demo::factory()->withSolution()->create();
        $this->assertInstanceOf(Solution::class, $demo->solution);
    }

    public function test_demo_has_an_organisation() {
        /* @var Demo $demo */
        $demo = Demo::factory()->withOrganisation()->create();
        $this->assertInstanceOf(Organisation::class, $demo->organisation);
    }

    public function test_demo_marked_as_finished() {
        /* @var Demo $demo */

        $demo = Demo::factory()->create();
        $this->assertNull($demo->finished_at);

        $demo->markAsFinished();
        $this->assertNotNull($demo->finished_at);
    }

    public function test_demo_is_finished() {
        /* @var Demo $demo */

        $demo = Demo::factory()->create();
        $this->assertFalse($demo->isFinished());

        $demo->markAsFinished();
        $this->assertTrue($demo->isFinished());
    }

    public function test_demo_is_running() {
        /* @var Demo $demo */

        $demo = Demo::factory()->create();
        $this->assertTrue($demo->isRunning());

        $demo->markAsFinished();
        $this->assertFalse($demo->isRunning());
    }

    public function test_demo_is_admin_demo() {
        /* @var Demo $demo */

        $demo = Demo::factory()->create();
        $this->assertFalse($demo->isAdminDemo());

        $demo = Demo::factory()->withAdminMode()->create();
        $this->assertTrue($demo->isAdminDemo());
    }

    public function test_demo_is_user_demo() {
        /* @var Demo $demo */

        $demo = Demo::factory()->create();
        $this->assertFalse($demo->isAdminDemo());

        $demo = Demo::factory()->withAdminMode()->create();
        $this->assertTrue($demo->isAdminDemo());
    }

    public function test_demo_create_main_demo() {
        /* @var Demo $demo */
        $solution = Solution::factory()->withLatestVersion()->create();
        $demo = Demo::createMainDemo($solution->latestVersion);
        $this->assertTrue($demo->main);
    }
}
