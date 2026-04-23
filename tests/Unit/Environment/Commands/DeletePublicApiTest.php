<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\DeletePublicApi;
use App\Environment\Commands\StorePublicApi;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class DeletePublicApiTest
 * @package Tests\Unit\Environment\Commands
 */
class DeletePublicApiTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_store_public_api_list() {
        $environment = Environment::factory()->make();
        $id = Str::uuid()->toString();
        $payload = [
            'id' => $id,
            'name' => 'a',
            'slug' => 'a',
            'type' => 'list',
        ];

        $this->assertCount(0, $environment->blueprint->publicApis);
        $environment = (new StorePublicApi($payload, $environment))->execute();

        $this->assertCount(1, $environment->blueprint->publicApis);

        $environment = (new DeletePublicApi(['id' => $id], $environment))->execute();

        $this->assertCount(0, $environment->blueprint->publicApis);
    }
}
