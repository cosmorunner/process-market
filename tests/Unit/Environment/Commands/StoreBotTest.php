<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\Command;
use App\Models\Environment;
use App\Environment\Commands\StoreBot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreBotTest
 * @package Tests\Unit\Environment\Commands
 */
class StoreBotTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_store_bot() {
        $name = 'StoreBot';
        $payload = ['aliases' => ["sd"], 'first_name' => 'sd'];
        $environment = Environment::factory()->create();

        $this->assertCount(0, $environment['blueprint']->bots);

        $command = Command::create($name, $payload, $environment);
        $this->assertInstanceOf(StoreBot::class, $command);
        $environment = $command->execute();
        $this->assertCount(1, $environment['blueprint']->bots);
    }
}
