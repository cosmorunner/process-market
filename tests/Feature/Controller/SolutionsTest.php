<?php

namespace Tests\Feature\Controller;

use App\Enums\Visibility;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class SolutionsTest
 * @package Tests\Feature\Routes
 */
class SolutionsTest extends TestCase {

    use WithFaker, RefreshDatabase;

    public function test_an_unauthenticated_user_cannot_create_a_solution() {
        $this->get(route('solution.create'))->assertRedirect(route('login'));
    }

    // TODO Es wird versucht eine Allisa Plattform Demo für die Lösung zu erzeugen. Dies funktioniert bei Unit-Tests nicht, der DemoConnector müsste "gemockt" werden.
    //    public function test_an_authenticated_user_can_create_a_solution() {
    //        /* @var Solution $solution */
    //        $solution = Solution::factory()->ofCreatorAndAuthor($this->login())->make();
    //        $this->get(route('solution.create'))->assertOk();
    //        $response = $this->post(route('api.solution.store'), $solution->toArray());
    //        $response->assertCreated();
    //        $response->assertJson(['redirect' => route('solution.edit', Solution::first())]);
    //    }

    public function test_a_user_can_archive_a_solution() {
        /* @var Solution $solution */
        $solution = Solution::factory()
            ->withLatestVersion()
            ->withoutLatestPublishedVersion()
            ->ofCreatorAndAuthor($this->login())
            ->create();
        $this->delete(route('solution.destroy', ['solution' => $solution]), ['accept' => 'on'])
            ->assertRedirect(route('profile.solutions'));
        $this->assertDatabaseMissing('solutions', $solution->only('id'));
    }

    public function test_an_unauthenticated_user_cannot_archive_solutions() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->delete(route('solution.destroy', ['solution' => $solution]))->assertRedirect(route('login'));
    }

    public function test_a_user_can_update_his_solution() {
        /* @var Solution $solution */
        $solution = Solution::factory()->ofCreatorAndAuthor($this->login())->create();

        $this->patch(route('api.solution.update', $solution), [
            'name' => 'Changed',
            'description' => 'Changed',
            'visibility' => Visibility::Private->value,
        ])->assertOk();

        $solution->refresh();
        $this->assertEquals('Changed', $solution->name);
        $this->assertEquals('Changed', $solution->description);
        $this->assertEquals(Visibility::Private->value, $solution->visibility);
    }

    public function test_an_authenticated_user_can_view_a_public_solution() {
        /* @var Solution $solution */
        $this->login();
        $solution = Solution::factory()
            ->withLatestPublishedSolutionVersion()
            ->ofCreatorAndAuthor(User::factory()->create())
            ->withVisibility(Visibility::Public->value)
            ->create();

        $this->get(route('solution.detail', [
            'namespace' => $solution->namespace,
            'identifier' => $solution->identifier
        ]))->assertOk();
    }

    public function test_an_authenticated_user_cannot_view_a_private_solution() {
        /* @var Solution $solution */
        $this->login();
        $solution = Solution::factory()
            ->withLatestPublishedSolutionVersion()
            ->ofCreatorAndAuthor(User::factory()->create())
            ->withVisibility(Visibility::Private->value)
            ->create();

        $this->get(route('solution.detail', [
            'namespace' => $solution->namespace,
            'identifier' => $solution->identifier
        ]))->assertOk()->assertViewIs('errors.403');
    }

}
