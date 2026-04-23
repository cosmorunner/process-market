<?php

namespace Tests\Feature\Controller;

use App\Enums\Visibility;
use App\Models\Process;
use App\Models\ProcessVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UsersTest
 * @package Tests\Feature\Routes
 */
class UsersTest extends TestCase {
    use RefreshDatabase;

    public function test_users_controller_user_can_sort_public_processes() {
        /* @var  $user */
        $user = $this->login();

        $processVersionZ = ProcessVersion::factory()->create(['complexity_score' => 2]);
        $processVersionA = ProcessVersion::factory()->create(['complexity_score' => 1]);
        $processVersionM = ProcessVersion::factory()->create(['complexity_score' => 3]);

        Process::factory()
            ->withLatestPublishedVersion($processVersionZ)
            ->withVisibility(Visibility::Public->value)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'Z Process'])
            ->setUpdatedAt(now()->subDays(3))
            ->save();
        Process::factory()
            ->withLatestPublishedVersion($processVersionA)
            ->withVisibility(Visibility::Public->value)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'A Process'])
            ->setUpdatedAt(now()->subDays())
            ->save();
        Process::factory()
            ->withLatestPublishedVersion($processVersionM)
            ->withVisibility(Visibility::Public->value)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'M Process'])
            ->setUpdatedAt(now()->subDays(2))
            ->save();

        // Sort for 'lastChange_desc'
        $response = $this->get(route('user.show', ['user' => $user, 'sort' => 'lastChange_desc']))->assertOk();
        $this->assertEquals('A Process', $response->viewData('processes')->first()->title);

        // Sort for 'alphabetically_desc'
        $response = $this->get(route('user.show', ['user' => $user, 'sort' => 'alphabetically_desc']))->assertOk();
        $this->assertEquals('Z Process', $response->viewData('processes')->first()->title);

        // Sort for 'complexity_desc'
        $response = $this->get(route('user.show', ['user' => $user, 'sort' => 'complexity_desc']))->assertOk();
        $this->assertEquals('M Process', $response->viewData('processes')->first()->title);
    }

    public function test_users_controller_user_can_search_public_processes() {
        /* @var  $user */
        $user = $this->login();

        $processVersionZ = ProcessVersion::factory()->create(['complexity_score' => 2]);
        $processVersionA = ProcessVersion::factory()->create(['complexity_score' => 1]);
        $processVersionM = ProcessVersion::factory()->create(['complexity_score' => 3]);

        Process::factory()
            ->withLatestPublishedVersion($processVersionZ)
            ->withVisibility(Visibility::Public->value)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'Z Process'])
            ->setUpdatedAt(now()->subDays(3))
            ->save();
        Process::factory()
            ->withLatestPublishedVersion($processVersionA)
            ->withVisibility(Visibility::Public->value)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'A Process'])
            ->setUpdatedAt(now()->subDays())
            ->save();
        Process::factory()
            ->withLatestPublishedVersion($processVersionM)
            ->withVisibility(Visibility::Public->value)
            ->ofCreatorAndAuthor($user)
            ->create(['title' => 'M Process'])
            ->setUpdatedAt(now()->subDays(2))
            ->save();

        // Search for 'Z'
        $response = $this->get(route('user.show', ['user' => $user, 'search' => 'Z']))->assertOk();
        $this->assertCount(1, $response->viewData('processes'));
        $this->assertEquals('Z Process', $response->viewData('processes')->first()->title);
    }
}