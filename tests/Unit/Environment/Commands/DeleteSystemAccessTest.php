<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\DeleteSystemAccess;
use App\Environment\Commands\StoreSystemAccess;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class DeleteSystemAccessTest
 * @package Tests\Unit\Environment\Commands
 */
class DeleteSystemAccessTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_delete_system_access() {
        $environment = Environment::factory()->make();
        $payload = [
            'user_id' => Str::uuid()->toString(),
            'role_id' => Str::uuid()->toString()
        ];

        $this->assertCount(0, $environment->blueprint->systemAccesses);
        $environment = (new StoreSystemAccess($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->systemAccesses);

        $environment = (new DeleteSystemAccess($payload, $environment))->execute();
        $this->assertCount(0, $environment->blueprint->systemAccesses);
    }
}
