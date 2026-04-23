<?php

namespace Tests\Unit\Models;

use App\Models\Plugin;
use App\Models\PluginVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class PluginVersionTest
 * @package Tests\Unit\Models
 */
class PluginVersionTest extends TestCase {

    use RefreshDatabase;

    public function test_plugin_version_has_an_id() {
        /* @var PluginVersion $pluginVersion */
        $pluginVersion = PluginVersion::factory()->create();
        $this->assertNotEmpty($pluginVersion->id);
    }

    public function test_plugin_version_has_a_plugin_id() {
        /* @var PluginVersion $pluginVersion */
        $pluginVersion = PluginVersion::factory()->create();
        $this->assertNotEmpty($pluginVersion->plugin_id);
    }

    public function test_plugin_version_has_data() {
        /* @var PluginVersion $pluginVersion */
        $pluginVersion = PluginVersion::factory()->create();
        $this->assertNotEmpty($pluginVersion->data);
    }

    public function test_plugin_version_has_a_version() {
        /* @var PluginVersion $pluginVersion */
        $pluginVersion = PluginVersion::factory()->create();
        $this->assertNotEmpty($pluginVersion->version);
    }

    public function test_plugin_version_has_a_changelog() {
        /* @var PluginVersion $pluginVersion */
        $pluginVersion = PluginVersion::factory()->create();
        $this->assertNotEmpty($pluginVersion->changelog);
    }

    public function test_plugin_version_has_a_publish_date() {
        /* @var PluginVersion $pluginVersion */
        $pluginVersion = PluginVersion::factory()->create();
        $this->assertNotEmpty($pluginVersion->published_at);
    }

    public function test_plugin_version_has_create_info() {
        /* @var PluginVersion $pluginVersion */
        $pluginVersion = PluginVersion::factory()->create();
        $this->assertNotEmpty($pluginVersion->created_at);
    }

    public function test_plugin_version_has_update_info() {
        /* @var PluginVersion $pluginVersion */
        $pluginVersion = PluginVersion::factory()->create();
        $this->assertNotEmpty($pluginVersion->updated_at);
    }

    public function test_plugin_version_belongs_to_a_plugin() {
        /* @var PluginVersion $pluginVersion */
        $pluginVersion = PluginVersion::factory()->ofPlugin()->create();
        $this->assertInstanceOf(Plugin::class, $pluginVersion->plugin);
    }

    public function test_plugin_version_is_published() {
        /* @var PluginVersion $pluginVersion */
        $pluginVersion = PluginVersion::factory()->create();

        $this->assertTrue($pluginVersion->isPublished());
    }

    public function test_plugin_has_full_namespace_attribute() {
        /* @var PluginVersion $pluginVersion */
        $pluginVersion = PluginVersion::factory()->create();
        $this->assertStringContainsString('@', $pluginVersion->full_namespace);
    }
}
