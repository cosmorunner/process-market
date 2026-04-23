<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreGroup;
use App\Environment\Group;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class StoreUserTest
 * @package Tests\Unit\Environment\Commands
 */
class StoreGroupTest extends TestCase {

    use RefreshDatabase;

    public function test_store_group_simple() {
        $environment = Environment::factory()->emptyWithName('Standard')->make();

        $payload = [
            'name' => 'Group 1',
            'aliases' => ['alias1'],
            'tags' => ['tag1'],
        ];

        $this->assertCount(0, $environment->blueprint->groups);
        $environment = (new StoreGroup($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->groups);

        $group = $environment->blueprint->groups->first();
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals('Group 1', $group->name);
        $this->assertEquals('alias1', $group->aliases[0]);
        $this->assertEquals('tag1', $group->tags[0]);
    }

    public function test_store_group_rule_valid_payload() {
        $environment = $this->fullySetupEnvironment();

        $payload = [
            'id' => Str::uuid()->toString(),
            'name' => 'Group 1',
            'aliases' => ['alias1'],
            'tags' => ['tag1'],
        ];

        $this->updateEnvironmentBlueprint($environment->processVersion, $environment, 'StoreGroup', $payload)->assertOk();
    }

    public function test_store_group_rule_missing_aliases() {
        $environment = $this->fullySetupEnvironment();

        $payload = [
            'id' => Str::uuid()->toString(),
            'name' => 'Group 1',
            'aliases' => [],
        ];

        $this->updateEnvironmentBlueprint($environment->processVersion, $environment, 'StoreGroup', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('aliases')
            ->decodeResponseJson()
            ->json();
    }

}
