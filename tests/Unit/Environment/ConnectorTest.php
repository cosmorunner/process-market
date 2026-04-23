<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment;

use App\Environment\Connector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class ConnectorTest
 * @package Tests\Unit\Environment
 */
class ConnectorTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_bot() {
        $options = [
            'id' => Str::uuid()->toString(),
            'name' => 'my_connector',
            'description' =>'',
            'identifier' =>'my_identifier',
            'type' => 'http',
            'base_uri' => 'https://example.com',
            'mode' => 'debug',
            'active' => true,
            'options' =>  [],
        ];

        $blueprint = Connector::make($options)->toArray();

        $this->assertEquals($blueprint['id'], $options['id']);
        $this->assertEquals($blueprint['name'], $options['name']);
        $this->assertEquals($blueprint['identifier'], $options['identifier']);
        $this->assertEquals($blueprint['type'], $options['type']);
        $this->assertEquals($blueprint['base_uri'], $options['base_uri']);
        $this->assertEquals($blueprint['mode'], $options['mode']);
        $this->assertEquals($blueprint['active'], $options['active']);
        $this->assertEquals($blueprint['options'], $options['options']);
    }
}
