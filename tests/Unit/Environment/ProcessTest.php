<?php

namespace Tests\Unit\Environment;

use App\Environment\Process;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class ProcessTest
 * @package Tests\Unit\Environment
 */
class ProcessTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_process() {
        $id = Str::uuid()->toString();
        $options = [
            'id' => $id,
            'process_type' => '',
            'name' => 'Prozess ' . substr($id, 0, 4),
            'description' => '',
            'image' => 'star',
            'tags' => '',
            'reference' => '',
            'initial_data' => [],
            'initial_situation' => [],
            'accesses' => [],
        ];

        $blueprint = Process::make($options)->toArray();

        $this->assertEquals($blueprint['id'], $options['id']);
        $this->assertEquals($blueprint['process_type'], $options['process_type']);
        $this->assertEquals($blueprint['name'], $options['name']);
        $this->assertEquals($blueprint['description'], $options['description']);
        $this->assertEquals($blueprint['image'], $options['image']);
        $this->assertEquals($blueprint['tags'], $options['tags']);
        $this->assertEquals($blueprint['reference'], $options['reference']);
        $this->assertEquals($blueprint['initial_data'], $options['initial_data']);
        $this->assertEquals($blueprint['initial_situation'], $options['initial_situation']);
        $this->assertEquals($blueprint['accesses'], $options['accesses']);
    }
}
