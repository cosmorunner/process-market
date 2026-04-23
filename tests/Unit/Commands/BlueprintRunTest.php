<?php

namespace Tests\Unit\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class BlueprintRunTest
 * @package Tests\Unit\Commands
 */
class BlueprintRunTest extends TestCase {

    use RefreshDatabase;

    // Robert: Vorläufig keine Tests implementieren.
    //    public function test_blueprint_run_bad_file() {
    //        $this->assertEquals(1, Artisan::call('app:blueprint_run', ['blueprintName' => 'does_not_exist']));
    //    }
    //
    //    public function test_blueprint_run_good_file() {
    //        $this->assertEquals(0, Artisan::call('app:blueprint_run', ['blueprintName' => 'empty']));
    //    }
}