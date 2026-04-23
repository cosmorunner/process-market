<?php

namespace Tests\Unit\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DockerInstallTest
 * @package Tests\Unit\Commands
 */
class DockerInstallTest extends TestCase {

    use RefreshDatabase;

    // Robert: Vorläufig keine Tests implementieren.
    //    public function test_docker_install_install_with_bad_file() {
    //        // The blueprint is only used if the users table does not exists
    //        Schema::table("users", function($table) {
    //            $table->drop();
    //        });
    //        $this->assertEquals(1, Artisan::call('app:docker_install', ['blueprintName' => 'does_not_exist']));
    //    }
    //
    //    public function test_docker_install_install_with_good_file() {
    //        // The blueprint is only used if the users table does not exists
    //        Schema::table("users", function($table) {
    //            $table->drop();
    //        });
    //        $this->assertEquals(0, Artisan::call('app:docker_install', ['blueprintName' => 'empty']));
    //    }
    //
    //    public function test_docker_install_system_exists_() {
    //        // By default the test blueprint is run for unittest.
    //        // Therefor a users table will exists resulting in an success as nothing needsd to be done
    //        $this->assertEquals(0, Artisan::call('app:docker_install', ['blueprintName' => 'does_not_matter']));
    //    }
}