<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreProcess;
use App\Environment\Commands\UpdateProcess;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class UpdateProcessTest
 * @package Tests\Unit\Environment\Commands
 */
class UpdateProcessTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_update_process() {
        $environment = Environment::factory()->make();
        $id = Str::uuid()->toString();
        $payload = [
            'accesses' => [],
            'id' => $id,
            'initial_data' => [],
            'initial_situation' => [],
            'name' => 'abc',
            'process_type' => 'robert/bbb@0.0.1'
        ];

        $this->assertCount(0, $environment->blueprint->processes);
        $environment = (new StoreProcess($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->processes);
        $this->assertEquals($payload['name'], $environment->blueprint->processes[0]->name);

        $payload = [
            'id' => $id,
            'name' => 'def',
        ];

        $environment = (new UpdateProcess($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->processes);
        $this->assertEquals($payload['name'], $environment->blueprint->processes[0]->name);
    }
}
