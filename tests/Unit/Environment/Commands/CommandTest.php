<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\Command;
use App\Environment\Commands\StoreUser;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class CommandTest
 * @package Tests\Unit\Environment\Commands
 */
class CommandTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_create_valid_command() {
        $name = "StoreUser";
        $payload = [];
        $environment = Environment::factory()->create();
        $command = Command::create($name, $payload, $environment);

        $this->assertInstanceOf(StoreUser::class, $command);
    }

    public function test_environment_command_create_invalid_command() {
        $name = "saasdfhs";
        $payload = [];
        $environment = Environment::factory()->create();
        $this->assertThrows(fn() => Command::create($name, $payload, $environment));
    }
}
