<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\DeleteTask;
use App\Environment\Commands\StoreTask;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class DeleteTaskTest
 * @package Tests\Unit\Environment\Commands
 */
class DeleteTaskTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_delete_task() {
        $environment = Environment::factory()->make();
        $payload = [
            'id' => Str::uuid()->toString(),
            'identifier' => 'identifier',
            'user' =>  Str::uuid()->toString(),
        ];
        $this->assertCount(0, $environment->blueprint->tasks);
        $environment = (new StoreTask($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->tasks);
        $environment = (new DeleteTask($payload, $environment))->execute();
        $this->assertCount(0, $environment->blueprint->tasks);
    }
}
