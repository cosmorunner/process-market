<?php

namespace Tests\Unit\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class FlushRedisTest
 * @package Tests\Unit\Commands
 */
class FlushRedisTest extends TestCase {

    use RefreshDatabase;

    // Robert: Vorläufig keine Tests implementieren.
    //    public function test_flush_redis() {
    //        $this->assertEquals(0, Artisan::call('app:session_flush'));
    //    }
}