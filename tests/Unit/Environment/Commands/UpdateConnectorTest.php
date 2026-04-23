<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\Command;
use App\Environment\Commands\UpdateConnector;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Guid\Guid;
use Tests\TestCase;

/**
 * Class UpdateConnectorTest
 * @package Tests\Unit\Environment\Commands
 */
class UpdateConnectorTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_update_connector_http() {
        $environment = Environment::factory()->create();
        $this->assertCount(0, $environment['blueprint']->connectors);

        $id =  Guid::uuid4()->toString();
        $environment = Command::create('StoreConnector', [
            'id' => $id,
            'identifier' => 'sdfsd',
            'name' => 'sdf',
            'type' => 'http'
        ], $environment)->execute();

        $this->assertCount(1, $environment['blueprint']->connectors);
        $this->assertEquals('sdf', $environment['blueprint']->connectors->first()->name);

        $environment = (new UpdateConnector([
            'id' => $id,
            'name' => 'my_connector',
            'description' => '',
            'identifier' => 'my_identifier',
            'type' => 'http',
        ], $environment))->execute();

        $this->assertCount(1, $environment['blueprint']->connectors);
        $this->assertEquals('my_connector', $environment['blueprint']->connectors->first()->name);
    }
}
