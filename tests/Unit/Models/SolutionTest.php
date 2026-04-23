<?php

namespace Tests\Unit\Models;

use App\Models\Solution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class SolutionTest
 * @package Tests\Unit\Models
 */
class SolutionTest extends TestCase {

    use RefreshDatabase;

    public function test_it_has_a_name() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsString($solution->name);
    }

    public function test_it_has_a_description() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsString($solution->description);
    }

    public function test_it_has_a_creator_id() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsString($solution->creator_id);
    }

    public function test_it_has_a_namespace() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsString($solution->namespace);
    }

    public function test_it_has_an_identifier() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsString($solution->identifier);
    }

    public function test_it_has_an_visibility() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsInt($solution->visibility);
    }

    public function test_it_has_a_latest_version() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsString($solution->latest_version);
    }

    public function test_it_has_a_author_id() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsString($solution->author_id);
    }

    public function test_it_has_a_author_type() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsString($solution->author_type);
    }

    public function test_it_has_a_latest_version_id() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsString($solution->latest_version_id);
    }

    public function test_it_has_a_latest_published_version_id() {
        /* @var Solution $solution */
        $solution = Solution::factory()->create();
        $this->assertIsString($solution->latest_published_version_id);
    }

    public function test_it_has_a_version() {
        /* @var Solution $solution */
        $solution = Solution::factory()->withLatestVersion()->create();
        $this->assertNotEmpty($solution->versions);
    }

    public function test_it_can_find_solution_by_namespace_and_identifier() {
        $solution = Solution::factory()->create();
        $this->assertInstanceOf(Solution::class, Solution::whereFullNamespace($solution->full_namespace)->first());
    }

    public function test_it_has_tags() {
        /* @var Solution $solution */
        $solution = Solution::factory()->withTags()->create();
        $this->assertInstanceOf(Collection::class, $solution->tags);
        $this->assertNotEmpty($solution->tags);
    }

}
