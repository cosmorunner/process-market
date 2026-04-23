<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment;

use App\Environment\GroupAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class GroupAccessTest
 * @package Tests\Unit\Environment
 */
class GroupAccessTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_bot() {
        $options = [
            'group_id' => '',
            'user_id' => '',
            'role_id' => ''
        ];

        $blueprint = GroupAccess::make($options)->toArray();

        $this->assertEquals($blueprint['group_id'], $options['group_id']);
        $this->assertEquals($blueprint['user_id'], $options['user_id']);
        $this->assertEquals($blueprint['role_id'], $options['role_id']);
    }
}
