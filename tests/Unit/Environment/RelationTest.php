<?php

namespace Tests\Unit\Environment;

use App\Environment\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class RelationTest
 * @package Tests\Unit\Environment
 */
class RelationTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_relation() {
        $id = Str::uuid()->toString();
        $options = [
            'id' => $id,
            'left' =>  '',
            'relation_type' =>'',
            'relation_type_name' => '',
            'right' => '',
            'data' =>[]
        ];

        $blueprint = Relation::make($options)->toArray();

        $this->assertEquals($blueprint['id'], $options['id']);
        $this->assertEquals($blueprint['left'], $options['left']);
        $this->assertEquals($blueprint['relation_type'], $options['relation_type']);
        $this->assertEquals($blueprint['relation_type_name'], $options['relation_type_name']);
        $this->assertEquals($blueprint['right'], $options['right']);
        $this->assertEquals($blueprint['data'], $options['data']);
    }
}
