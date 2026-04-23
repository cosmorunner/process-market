<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\Command;
use App\Environment\Commands\StoreConnector;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Guid\Guid;
use Tests\TestCase;

/**
 * Class StoreConnectorTest
 * @package Tests\Unit\Environment\Commands
 */
class StoreConnectorTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_store_connector_http() {
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
    }

    public function test_environment_command_store_connector_soap() {
        $environment = Environment::factory()->create();
        $this->assertCount(0, $environment['blueprint']->connectors);

        $command = Command::create('StoreConnector', [
            'id' => Guid::uuid4()->toString(),
            'identifier' => 'sdfsd',
            'name' => 'sdf',
            'type' => 'soap'
        ], $environment);

        $this->assertInstanceOf(StoreConnector::class, $command);

        $environment = $command->execute();
        $this->assertCount(1, $environment['blueprint']->connectors);
    }

    public function test_environment_command_store_connector_sftp() {
        $environment = Environment::factory()->create();
        $this->assertCount(0, $environment['blueprint']->connectors);

        $command = Command::create('StoreConnector', [
            'id' => Guid::uuid4()->toString(),
            'identifier' => 'sdfsd',
            'name' => 'sdf',
            'type' => 'sftp'
        ], $environment);

        $this->assertInstanceOf(StoreConnector::class, $command);

        $environment = $command->execute();
        $this->assertCount(1, $environment['blueprint']->connectors);
    }
    public function test_environment_command_store_connector_database() {
        $environment = Environment::factory()->create();
        $this->assertCount(0, $environment['blueprint']->connectors);

        $command = Command::create('StoreConnector', [
            'id' => Guid::uuid4()->toString(),
            'identifier' => 'sdfsd',
            'name' => 'sdf',
            'type' => 'database'
        ], $environment);

        $this->assertInstanceOf(StoreConnector::class, $command);

        $environment = $command->execute();
        $this->assertCount(1, $environment['blueprint']->connectors);
    }
}
