<?php

namespace Tests\Unit\Models;

use App\Models\Solution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SolutionLicenseTest
 * @package Tests\Unit\Models
 */
class SolutionLicenseTest extends TestCase {

    use RefreshDatabase;

    public function test_solution_license_has_a_name() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsString($solution->name);
    }
}
