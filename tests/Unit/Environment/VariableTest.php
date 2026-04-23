<?php

namespace Tests\Unit\Environment;

use App\Environment\Variable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class VariableTest
 * @package Tests\Unit\Environment
 */
class VariableTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_system_task() {
        $options = [
            'id' => Str::uuid()->toString(),
            'identifier' => '',
            'type' =>  'TYPE_STRING',
            'value' => '',
            'is_public' => false
        ];

        $blueprint = Variable::make($options)->toArray();

        $this->assertEquals($blueprint['id'], $options['id']);
        $this->assertEquals($blueprint['identifier'], $options['identifier']);
        $this->assertEquals($blueprint['type'], $options['type']);
        $this->assertEquals($blueprint['value'], $options['value']);
        $this->assertEquals($blueprint['is_public'], $options['is_public']);
    }
}
