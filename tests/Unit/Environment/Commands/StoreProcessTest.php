<?php

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreProcess;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreProcessTest
 * @package Tests\Unit\Environment\Commands
 */
class StoreProcessTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_store_process() {
        $environment = Environment::factory()->make();
        $payload = [
            'accesses' => [],
            'id' => 'ad15db2c-5714-4b3a-8d50-de0fa25db847',
            'initial_data' => [],
            'initial_situation' => [],
            'name' => 'abbbb',
            'process_type' => 'robert/bbb@0.0.1'
        ];
        $this->assertCount(0, $environment->blueprint->processes);
        $environment = (new StoreProcess($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->processes);
    }
}
