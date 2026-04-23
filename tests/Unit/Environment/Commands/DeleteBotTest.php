<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\Command;
use App\Environment\Commands\DeleteBot;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteBotTest
 * @package Tests\Unit\Environment\Commands
 */
class DeleteBotTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_delete_bot() {
        $environment = Environment::factory()->create();
        $storeCommand = Command::create('StoreBot', ['aliases' => ['sd'], 'first_name' => 'sd'], $environment);

        $environment = $storeCommand->execute();
        $this->assertCount(1, $environment['blueprint']->bots);

        $command = Command::create('DeleteBot', ['id' => $environment['blueprint']->bots[0]->id], $environment);
        $this->assertInstanceOf(DeleteBot::class, $command);
        $environment = $command->execute();
        $this->assertCount(0, $environment['blueprint']->bots);
    }
}
