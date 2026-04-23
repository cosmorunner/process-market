<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\Command;
use App\Environment\Commands\DeleteConnector;
use App\Environment\Commands\StoreConnector;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Guid\Guid;
use Tests\TestCase;

/**
 * Class DeleteConnectorTest
 * @package Tests\Unit\Environment\Commands
 */
class DeleteConnectorTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_delete_connector_http() {
        $environment = Environment::factory()->create();
        $this->assertCount(0, $environment['blueprint']->connectors);

        $command = Command::create('StoreConnector', [
            'id' => Guid::uuid4()->toString(),
            'identifier' => 'sdfsd',
            'name' => 'sdf',
            'type' => 'http'
        ], $environment);

        $this->assertInstanceOf(StoreConnector::class, $command);

        $environment = $command->execute();
        $this->assertCount(1, $environment['blueprint']->connectors);

        $command = Command::create('DeleteConnector', ['id' => $environment['blueprint']->connectors[0]->id], $environment);
        $this->assertInstanceOf(DeleteConnector::class, $command);

        $environment = $command->execute();
        $this->assertCount(0, $environment['blueprint']->connectors);
    }
}
