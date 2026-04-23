<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreTask;
use App\Environment\Commands\UpdateTask;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class UpdateTaskTest
 * @package Tests\Unit\Environment\Commands
 */
class UpdateTaskTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_update_task() {
        $environment = Environment::factory()->make();
        $id =  Str::uuid()->toString();
        $payload = [
            'id' => $id,
            'identifier' => 'identifier',
            'user' =>  Str::uuid()->toString(),
        ];
        $this->assertCount(0, $environment->blueprint->tasks);
        $environment = (new StoreTask($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->tasks);
        $this->assertEquals('identifier', $environment->blueprint->tasks[0]->identifier);

        $payload['identifier'] = 'identifier2';
        $environment = (new UpdateTask($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->tasks);
        $this->assertEquals('identifier2', $environment->blueprint->tasks[0]->identifier);
    }
}
