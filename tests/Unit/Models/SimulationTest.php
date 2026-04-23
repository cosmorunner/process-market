<?php

namespace Tests\Unit\Models;

use App\Models\Simulation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SimulationTest
 * @package Tests\Unit\Models
 */
class SimulationTest extends TestCase {

    use RefreshDatabase;

    public function test_simulation_has_an_id() {
        /* @var Simulation $simulation */
        $simulation = Simulation::factory()->create();
        $this->assertIsString($simulation->id);
    }
}