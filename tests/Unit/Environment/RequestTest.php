<?php

namespace Tests\Unit\Environment;

use App\Environment\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

/**
 * Class RequestTest
 * @package Tests\Unit\Environment
 */
class RequestTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_relation() {
        $id = Str::uuid()->toString();
        $options = [
            'id' => $id,
            'connector_id' => Uuid::uuid4()->toString(),
            'name' => 'my_connector',
            'description' => '',
            'identifier' => 'my_identifier',
            'uri' => '/',
            'method' => 'GET',
            'active' => true,
            'bindings' => [],
            'headers' => [],
            'body' => [],
            'validation' => [],
            'debug_options' => [],
        ];

        $blueprint = Request::make($options)->toArray();

        $this->assertEquals($blueprint['id'], $options['id']);
        $this->assertEquals($blueprint['connector_id'], $options['connector_id']);
        $this->assertEquals($blueprint['name'], $options['name']);
        $this->assertEquals($blueprint['description'], $options['description']);
        $this->assertEquals($blueprint['identifier'], $options['identifier']);
        $this->assertEquals($blueprint['uri'], $options['uri']);
        $this->assertEquals($blueprint['method'], $options['method']);
        $this->assertEquals($blueprint['active'], $options['active']);
        $this->assertEquals($blueprint['bindings'], $options['bindings']);
        $this->assertEquals($blueprint['headers'], $options['headers']);
        $this->assertEquals($blueprint['body'], $options['body']);
        $this->assertEquals($blueprint['validation'], $options['validation']);
        $this->assertEquals($blueprint['debug_options'], $options['debug_options']);
    }
}
