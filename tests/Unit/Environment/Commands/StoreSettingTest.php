<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreRelation;
use App\Environment\Commands\StoreSetting;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class StoreSettingTest
 * @package Tests\Unit\Environment\Commands
 */
class StoreSettingTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_store_settings() {
        $environment = Environment::factory()->make();
        $payload = [
            'name' => '1',
            'value' => '2',
            'owner_id' => null,
            'owner_type' => null
        ];
        $this->assertCount(0, $environment->blueprint->settings);
        $environment = (new StoreSetting($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->settings);
    }
}
