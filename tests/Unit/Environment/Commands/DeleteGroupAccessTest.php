<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\DeleteGroupAccess;
use App\Environment\GroupAccess;
use App\Models\Environment;
use Database\Builder\BlueprintBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class DeleteGroupAccessTest
 * @package Tests\Unit\Environment\Commands
 */
class DeleteGroupAccessTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_delete_group_access() {
        $userId = Str::uuid()->toString();

        $groupAcces = new GroupAccess([
            'user_id' => $userId,
            'role_id' => Str::uuid()->toString(),
            'group_id' => Str::uuid()->toString(),
        ]);

        $blueprint = app(BlueprintBuilder::class)->make(['group_accesses' => [$groupAcces->toArray()]]);

        /* @var Environment $environment */
        $environment = Environment::factory()->withBlueprint($blueprint)->create();

        $this->assertCount(1, $environment->blueprint->groupAccesses);

        $environment = (new DeleteGroupAccess(['user_id' => $userId], $environment))->execute();

        $this->assertCount(0, $environment->blueprint->groupAccesses);
    }
}
