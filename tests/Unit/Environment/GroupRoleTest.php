<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment;

use App\Environment\GroupRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class GroupRoleTest
 * @package Tests\Unit\Environment
 */
class GroupRoleTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_bot() {
        $options = [
            'id' => Str::uuid()->toString(),
            'group_id' => Str::uuid()->toString(),
            'name' => 'Demo',
            'locked' => true,
        ];

        $blueprint = GroupRole::make($options)->toArray();

        $this->assertEquals($blueprint['id'], $options['id']);
        $this->assertEquals($blueprint['name'], $options['name']);
        $this->assertEquals($blueprint['locked'], $options['locked']);
        $this->assertEquals($blueprint['group_id'], $options['group_id']);
    }
}
