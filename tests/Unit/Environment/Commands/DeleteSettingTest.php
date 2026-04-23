<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\DeleteSetting;
use App\Environment\Commands\StoreSetting;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteSettingTest
 * @package Tests\Unit\Environment\Commands
 */
class DeleteSettingTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_delete_settings() {
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
        $environment = (new DeleteSetting(['owner_id' => $environment->blueprint->settings->first()->owner_id], $environment))->execute();
        $this->assertCount(0, $environment->blueprint->settings);
    }
}
