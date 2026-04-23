<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\Api;

use App\Enums\Visibility;
use App\Models\Process;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ProcessesTest
 * @package Tests\Feature\Api
 */
class ProcessesControllerTest extends TestCase {

    use RefreshDatabase;

    public function test_processes_controller_process_can_be_archived() {
        $user = $this->login();
        $process = Process::factory()
            ->withVisibility(Visibility::Public->value)
            ->withPublishedVersion()->ofCreatorAndAuthor($user)
            ->create();

        $this->delete(route('process.destroy', ['process' => $process]), ['accept' => 'on']);

        $process->refresh();

        $this->assertTrue($process->isArchived());
        $this->assertEquals(Visibility::Hidden->value, $process->visibility);
    }

    public function test_processes_controller_process_can_be_updated() {
        $user = $this->login();

        $process = Process::factory()
            ->withVisibility(Visibility::Public->value)
            ->withLicense()
            ->withPublishedVersion()
            ->ofCreatorAndAuthor($user)
            ->create();

        $this->patch(route('api.process.update', ['process' => $process]), [
            'title' => $process->title,
            'visibility' => Visibility::Public->value,
            'license_options' => $process->license_options,
            'accept_license' => true
        ])->assertOk();
    }

    public function test_processes_controller_archived_process_cannot_be_updated() {
        $user = $this->login();

        $process = Process::factory()
            ->withVisibility(Visibility::Public->value)
            ->withPublishedVersion()
            ->archived()
            ->ofCreatorAndAuthor($user)
            ->create();

        $this->patch(route('api.process.update', ['process' => $process]), ['title' => $process->title])->assertForbidden();
    }

    public function test_processes_controller_archived_can_be_restored() {
        $user = $this->login();
        $process = Process::factory()->withLatestVersion()->ofCreatorAndAuthor($user)->archived()->create();

        $this->followingRedirects()
            ->patch(route('process.restore.execute', ['process' => $process]), ['accept' => 'on'])->assertOk()
            ->assertSee($process->title);

        $process->refresh();
        $this->assertFalse($process->isArchived());
    }

    public function test_processes_controller_archived_process_is_restored_with_correct_visibility() {
        $user = $this->login();
        // Check for Public (Should be hidden after restore)
        $process = Process::factory()->ofCreatorAndAuthor($user)->create(['visibility' => Visibility::Public]);

        $this->delete(route('process.destroy', ['process' => $process]), ['accept' => 'on']);
        $process->refresh();
        $this->assertTrue($process->isArchived());

        $this->patch(route('process.restore.execute', ['process' => $process]), ['accept' => 'on']);
        $process->refresh();
        $this->assertFalse($process->isArchived());
        $this->assertEquals(Visibility::Hidden->value, $process->visibility);

        // Check for Hidden
        $process = Process::factory()->ofCreatorAndAuthor($user)->create(['visibility' => Visibility::Hidden]);

        $this->delete(route('process.destroy', ['process' => $process]), ['accept' => 'on']);
        $process->refresh();
        $this->assertTrue($process->isArchived());

        $this->patch(route('process.restore.execute', ['process' => $process]), ['accept' => 'on']);
        $process->refresh();
        $this->assertFalse($process->isArchived());
        $this->assertEquals(Visibility::Hidden->value, $process->visibility);

        // Check for Private
        $process = Process::factory()->ofCreatorAndAuthor($user)->create(['visibility' => Visibility::Private]);

        $this->delete(route('process.destroy', ['process' => $process]), ['accept' => 'on']);
        $process->refresh();
        $this->assertTrue($process->isArchived());

        $this->patch(route('process.restore.execute', ['process' => $process]), ['accept' => 'on']);
        $process->refresh();
        $this->assertFalse($process->isArchived());
        $this->assertEquals(Visibility::Private->value, $process->visibility);
    }

}