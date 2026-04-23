<?php

namespace Tests\Unit\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class FlushSessionsTest
 * @package Tests\Unit\Commands
 */
class FlushSessionsTest extends TestCase {

    use RefreshDatabase;

    // Robert: Vorläufig keine Tests implementieren.
    //    public function test_flush_redis() {
    //        $this->assertEquals(0, Artisan::call('redis:flushdb'));
    //    }
}