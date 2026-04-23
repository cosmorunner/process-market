<?php

namespace Tests\Feature\Controller;

use App\Enums\Visibility;
use App\Models\Process;
use App\Models\ProcessVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ProfileTest
 * @package Tests\Feature\Routes
 */
class ProfileTest extends TestCase {

    use RefreshDatabase;

    public function test_an_unauthenticated_user_cannot_visit_the_profile() {
        $this->get(route('profile.processes'))->assertRedirect(route('login'));
        $this->delete(route('profile.close'))->assertRedirect(route('login'));
    }

    public function test_an_autenticated_user_can_view_the_profile() {
        $this->login();
        $this->get(route('profile.processes'))->assertOk();
    }

    public function test_the_profile_shows_processes_of_all_visibilities() {
        $user = $this->login();
        $private = Process::factory()
            ->withLatestVersion()
            ->ofCreatorAndAuthor($user)
            ->withVisibility(Visibility::Private->value)
            ->create();
        $hidden = Process::factory()
            ->withLatestVersion()
            ->ofCreatorAndAuthor($user)
            ->withVisibility(Visibility::Hidden->value)
            ->create();
        $public = Process::factory()
            ->withLatestVersion()
            ->ofCreatorAndAuthor($user)
            ->withVisibility(Visibility::Public->value)
            ->create();

        $this->get(route('profile.processes'))->assertSee($private->title);
        $this->get(route('profile.processes'))->assertSee($hidden->title);
        $this->get(route('profile.processes'))->assertSee($public->title);
    }

    public function test_a_user_can_close_their_account() {
        $this->login();
        $this->delete(route('profile.close'))->assertSessionHasErrors('accept');
    }

    /** @noinspection PhpRedundantOptionalArgumentInspection */
    public function test_profile_controller_user_can_sort_processes() {
        /* @var  $user */
        $user = $this->login();

        $processVersionZ = ProcessVersion::factory()->create(['complexity_score' => 2]);
        $processVersionA = ProcessVersion::factory()->create(['complexity_score' => 1]);
        $processVersionM = ProcessVersion::factory()->create(['complexity_score' => 3]);

        Process::factory()
            ->withLatestVersion($processVersionZ)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'Z Process'])
            ->setUpdatedAt(now()->subDays(3))
            ->save();
        Process::factory()
            ->withLatestVersion($processVersionA)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'A Process'])
            ->setUpdatedAt(now()->subDays(1))
            ->save();
        Process::factory()
            ->withLatestVersion($processVersionM)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'M Process'])
            ->setUpdatedAt(now()->subDays(2))
            ->save();

        // Sort for 'lastChange_desc'
        $response = $this->get(route('profile.processes', ['sort' => 'lastChange_desc']))->assertOk();
        $this->assertEquals('A Process', $response->viewData('processes')->first()->title);

        // Sort for 'alphabetically_desc'
        $response = $this->get(route('profile.processes', ['sort' => 'alphabetically_desc']))->assertOk();
        $this->assertEquals('Z Process', $response->viewData('processes')->first()->title);

        // Sort for 'complexity_desc'
        $response = $this->get(route('profile.processes', ['sort' => 'complexity_desc']))->assertOk();
        $this->assertEquals('M Process', $response->viewData('processes')->first()->title);
    }

    /** @noinspection PhpRedundantOptionalArgumentInspection */
    public function test_profile_controller_user_can_search_processes() {
        /* @var  $user */
        $user = $this->login();

        $processVersionZ = ProcessVersion::factory()->create(['complexity_score' => 2]);
        $processVersionA = ProcessVersion::factory()->create(['complexity_score' => 1]);
        $processVersionM = ProcessVersion::factory()->create(['complexity_score' => 3]);

        Process::factory()
            ->withLatestVersion($processVersionZ)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'Z Process'])
            ->setUpdatedAt(now()->subDays(3))
            ->save();
        Process::factory()
            ->withLatestVersion($processVersionA)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'A Process'])
            ->setUpdatedAt(now()->subDays(1))
            ->save();
        Process::factory()
            ->withLatestVersion($processVersionM)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'M Process'])
            ->setUpdatedAt(now()->subDays(2))
            ->save();

        // Search for 'Z'
        $response = $this->get(route('profile.processes', ['search' => 'Z']))->assertOk();
        $this->assertCount(1, $response->viewData('processes'));
        $this->assertEquals('Z Process', $response->viewData('processes')->first()->title);
    }

    public function test_profile_controller_user_can_display_archived_processes() {
        /* @var  $user */
        $user = $this->login();

        /* @var Process $notArchived */
        $notArchived = Process::factory()
            ->withPublishedVersion()
            ->withLatestVersion()
            ->ofCreatorAndAuthor($user)
            ->withVisibility(Visibility::Private->value)
            ->create();

        /* @var Process $archived */
        $archived = Process::factory()
            ->withPublishedVersion()
            ->withLatestVersion()
            ->ofCreatorAndAuthor($user)
            ->withVisibility(Visibility::Private->value)
            ->archived()
            ->create();

        // HTML Response of profile processes should not have archived process
        $this->get(route('profile.processes'))->assertOk()->assertSee($notArchived->title)->assertDontSee($archived->title);

        // Query for achived processes by adding the query parameter
        $this->get(route('profile.processes', ['archived' => 1]))
            ->assertSee($archived->title)
            ->assertDontSee($notArchived->title);
    }
}