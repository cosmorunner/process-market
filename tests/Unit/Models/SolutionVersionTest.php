<?php

namespace Tests\Unit\Models;

use App\Models\Solution;
use App\Models\SolutionVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SolutionVersionTest
 * @package Tests\Unit\Models
 */
class SolutionVersionTest extends TestCase {

    use RefreshDatabase;

    public function test_it_has_a_solution_id() {
        /* @var SolutionVersion $version */
        $version = SolutionVersion::factory()->create();
        $this->assertIsString($version->solution_id);
    }

    public function test_it_has_data() {
        /* @var SolutionVersion $version */
        $version = SolutionVersion::factory()->create();
        $this->assertIsArray($version->data);
    }

    public function test_it_has_a_version() {
        /* @var SolutionVersion $version */
        $version = SolutionVersion::factory()->create();
        $this->assertIsString($version->version);
    }

    public function test_it_has_a_changelog() {
        /* @var SolutionVersion $version */
        $version = SolutionVersion::factory()->create();
        $this->assertIsString($version->changelog);
    }

    public function test_it_has_a_published_at_date() {
        /* @var SolutionVersion $version */
        $version = SolutionVersion::factory()->create();
        $this->assertNull($version->published_at);
    }

    public function test_it_has_a_solution() {
        /* @var SolutionVersion $version */
        $version = SolutionVersion::factory()->ofSolution(Solution::factory()->create())->create();
        $this->assertInstanceOf(Solution::class, $version->solution);
    }

}
