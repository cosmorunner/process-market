<?php

namespace Tests\Unit\Models;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class TagTest
 * @package Tests\Unit\Models
 */
class TagTest extends TestCase {

    use RefreshDatabase;

    public function test_it_has_a_name() {
        /* @var Tag $tag */
        $tag = Tag::factory()->create();
        $this->assertIsString($tag->name);
    }

    public function test_it_has_a_color() {
        /* @var Tag $tag */
        $tag = Tag::factory()->create();
        $this->assertContains($tag->color, config('colors'));
    }

    public function test_it_has_processes() {
        /* @var Tag $tag */
        $tag = Tag::factory()->withProcess()->create();

        $this->assertInstanceOf(Collection::class, $tag->processes);
        $this->assertNotEmpty($tag->processes);
    }
}
