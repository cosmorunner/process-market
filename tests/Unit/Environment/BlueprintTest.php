<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment;

use App\Environment\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class BlueprintTest
 * @package Tests\Unit\Environment
 */
class BlueprintTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_blueprint() {
        $options = [
            'users' => [],
            'bots' => [],
            'groups' => [],
            'processes' => [],
            'relations' => [],
            'connectors' => [],
            'requests' => [],
            'groupAccesses' => [],
            'systemAccesses' => [],
            'settings' => [],
            'groupRoles' => [],
            'publicApis' => [],
            'variables' => [],
            'tasks' => []
        ];

        $blueprint = Blueprint::make($options)->toArray();

        $this->assertEquals($blueprint['users'], $options['users']);
        $this->assertEquals($blueprint['bots'], $options['bots']);
        $this->assertEquals($blueprint['groups'], $options['groups']);
        $this->assertEquals($blueprint['processes'], $options['processes']);
        $this->assertEquals($blueprint['relations'], $options['relations']);
        $this->assertEquals($blueprint['connectors'], $options['connectors']);
        $this->assertEquals($blueprint['requests'], $options['requests']);
        $this->assertEquals($blueprint['group_accesses'], $options['groupAccesses']);
        $this->assertEquals($blueprint['system_accesses'], $options['systemAccesses']);
        $this->assertEquals($blueprint['settings'], $options['settings']);
        $this->assertEquals($blueprint['group_roles'], $options['groupRoles']);
        $this->assertEquals($blueprint['public_apis'], $options['publicApis']);
        $this->assertEquals($blueprint['variables'], $options['variables']);
        $this->assertEquals($blueprint['tasks'], $options['tasks']);
    }
}
