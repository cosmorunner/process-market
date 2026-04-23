<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\DeleteGroup;
use App\Environment\Commands\StoreGroup;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteGroupTest
 * @package Tests\Unit\Environment\Commands
 */
class DeleteGroupTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_store_group_access() {
        $environment = Environment::factory()->emptyWithName('Standard')->make();

        $payload = [
            'name' => 'Group 1',
            'aliases' => ['alias1'],
            'tags' => ['tag1'],
        ];

        $this->assertCount(0, $environment->blueprint->groups);
        $environment = (new StoreGroup($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->groups);

        $payload = ['id' => $environment->blueprint->groups->first()->id,];

        $environment = (new DeleteGroup($payload, $environment))->execute();
        $this->assertCount(0, $environment->blueprint->groups);
    }
}
