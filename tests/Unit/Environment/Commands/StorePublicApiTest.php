<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StorePublicApi;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class StorePublicApiTest
 * @package Tests\Unit\Environment\Commands
 */
class StorePublicApiTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_store_public_api_list() {
        $environment = Environment::factory()->make();
        $payload = [
            'id' => Str::uuid()->toString(),
            'name' => 'a',
            'slug' => 'a',
            'type' => 'list',
        ];
        $this->assertCount(0, $environment->blueprint->publicApis);
        $environment = (new StorePublicApi($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->publicApis);
    }

    public function test_environment_command_public_api_action() {
        $environment = Environment::factory()->make();
        $payload = [
            'id' => Str::uuid()->toString(),
            'name' => 'a',
            'slug' => 'a',
            'type' => 'action',
        ];
        $this->assertCount(0, $environment->blueprint->publicApis);
        $environment = (new StorePublicApi($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->publicApis);
    }

    public function test_environment_command_public_api_initial_action() {
        $environment = Environment::factory()->make();
        $payload = [
            'id' => Str::uuid()->toString(),
            'name' => 'a',
            'slug' => 'a',
            'type' => 'initial_action',
        ];
        $this->assertCount(0, $environment->blueprint->publicApis);
        $environment = (new StorePublicApi($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->publicApis);
    }

    public function test_environment_command_public_api_process() {
        $environment = Environment::factory()->make();
        $payload = [
            'id' => Str::uuid()->toString(),
            'name' => 'a',
            'slug' => 'a',
            'type' => 'process',
        ];
        $this->assertCount(0, $environment->blueprint->publicApis);
        $environment = (new StorePublicApi($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->publicApis);
    }
}
