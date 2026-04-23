<?php

namespace Tests\Unit\Environment;

use App\Environment\SystemAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SystemAccessTest
 * @package Tests\Unit\Environment
 */
class SystemAccessTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_system_access() {
        $options = [
            'user_id' =>'',
            'role_id' =>  ''
        ];

        $blueprint = SystemAccess::make($options)->toArray();

        $this->assertEquals($blueprint['user_id'], $options['user_id']);
        $this->assertEquals($blueprint['role_id'], $options['role_id']);
    }
}
