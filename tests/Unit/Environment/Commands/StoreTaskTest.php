<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreTask;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class StoreTaskTest
 * @package Tests\Unit\Environment\Commands
 */
class StoreTaskTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_store_task() {
        $environment = Environment::factory()->make();
        $payload = [
            'id' => Str::uuid()->toString(),
            'identifier' => 'identifier',
            'user' =>  Str::uuid()->toString(),
        ];
        $this->assertCount(0, $environment->blueprint->tasks);
        $environment = (new StoreTask($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->tasks);
    }
}
