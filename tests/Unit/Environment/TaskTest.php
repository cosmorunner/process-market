<?php

namespace Tests\Unit\Environment;

use App\Environment\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class TaskTest
 * @package Tests\Unit\Environment
 */
class TaskTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_system_task() {
        $options = [
            'id' => Str::uuid()->toString(),
            'identifier' => $options['identifier'] ?? '',
            'user' => $options['user'] ?? '',
        ];

        $blueprint = Task::make($options)->toArray();

        $this->assertEquals($blueprint['id'], $options['id']);
        $this->assertEquals($blueprint['identifier'], $options['identifier']);
        $this->assertEquals($blueprint['user'], $options['user']);
    }
}
