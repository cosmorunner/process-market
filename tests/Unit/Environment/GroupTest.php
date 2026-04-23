<?php

namespace Tests\Unit\Environment;

use App\Environment\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class GroupTest
 * @package Tests\Unit\Environment
 */
class GroupTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_bot() {
        $options = [
            'id' => Str::uuid()->toString(),
            'name' => '',
            'provider' => null,
            'provider_group_id' => null,
            'aliases' => [],
            'tags' =>  [],
            'identity_process_type' =>  '',
            'identity_process_instance' => '',
        ];

        $blueprint = Group::make($options)->toArray();

        $this->assertEquals($blueprint['id'], $options['id']);
        $this->assertEquals($blueprint['name'], $options['name']);
        $this->assertEquals($blueprint['provider'], $options['provider']);
        $this->assertEquals($blueprint['provider_group_id'], $options['provider_group_id']);
        $this->assertEquals($blueprint['aliases'], $options['aliases']);
        $this->assertEquals($blueprint['tags'], $options['tags']);
        $this->assertEquals($blueprint['identity_process_type'], $options['identity_process_type']);
        $this->assertEquals($blueprint['identity_process_instance'], $options['identity_process_instance']);
    }
}
