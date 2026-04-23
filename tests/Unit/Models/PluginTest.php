<?php

namespace Tests\Unit\Models;

use App\Enums\PluginSource;
use App\Enums\PluginType;
use App\Models\Organisation;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class PluginTest
 * @package Tests\Unit\Models
 */
class PluginTest extends TestCase {
    use RefreshDatabase;

    public function test_Plugin_has_an_id() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->id);
    }

    public function test_Plugin_has_a_name() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->name);
    }

    public function test_Plugin_has_a_type() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->type);
    }

    public function test_Plugin_has_a_source() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->source);
    }

    public function test_Plugin_has_a_creator_id() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->creator_id);
    }

    public function test_Plugin_has_a_namespace() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->namespace);
    }

    public function test_Plugin_has_an_identifier() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->identifier);
    }

    public function test_Plugin_is_enabled() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertTrue($plugin->enabled);
    }

    public function test_Plugin_has_a_latest_version() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->latest_version);
    }

    public function test_Plugin_has_an_author_id() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->author_id);
    }

    public function test_Plugin_has_an_author_type() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->author_type);
    }

    public function test_Plugin_has_data() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->data[0]);
    }

    public function test_Plugin_has_a_latest_version_id() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->latest_version_id);
    }

    public function test_Plugin_has_a_latest_published_version_id() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertNotEmpty($plugin->latest_published_version_id);
    }

    public function test_plugin_is_authored_by_organisation() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $plugin->author_type = Organisation::class;
        $this->assertTrue($plugin->authoredByOrganisation());
    }

    public function test_plugin_is_authored_by_user() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $plugin->author_type = User::class;
        $this->assertTrue($plugin->authoredByUser());
    }

    public function test_plugin_gets_full_namespace_attribute() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $this->assertStringContainsString('/', $plugin->full_namespace);
    }

    public function test_plugin_is_action_type_component() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $plugin->type = PluginType::ActionTypeComponent->value;
        $this->assertTrue($plugin->isActionTypeComponentPlugin());
    }

    public function test_plugin_is_Status_type() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $plugin->type = PluginType::StatusType->value;
        $this->assertTrue($plugin->isStatusTypePlugin());
    }

    public function test_plugin_is_custom_processor_component() {
        /* @var Plugin $plugin */
        $plugin = Plugin::factory()->create();
        $plugin->type = PluginType::CustomProcessor->value;
        $this->assertTrue($plugin->isCustomProcessorPlugin());
    }

    public function test_plugin_get_scope_action_type_component() {
        /* @var Plugin $plugin */
        /* @var Builder $query */
        $plugin = Plugin::factory()->create();
        $plugin->type = PluginType::CustomProcessor->value;
        $plugin->save();

        $query = Plugin::query();
        Plugin::scopeActionTypeComponent($query);
        $query->where('id', $plugin->id);
        $this->assertCount(0, $query->get());

        $plugin = Plugin::factory()->create();
        $plugin->type = PluginType::ActionTypeComponent->value;
        $plugin->save();

        $query = Plugin::query();
        Plugin::scopeActionTypeComponent($query);
        $query->where('id', $plugin->id);
        $this->assertCount(1, $query->get());
    }

    public function test_plugin_get_scope_custom_processor() {
        /* @var Plugin $plugin */
        /* @var Builder $query */
        $plugin = Plugin::factory()->create();
        $plugin->type = PluginType::ActionTypeComponent->value;
        $plugin->save();

        $query = Plugin::query();
        Plugin::scopeCustomProcessors($query);
        $query->where('id', $plugin->id);
        $this->assertCount(0, $query->get());

        $plugin = Plugin::factory()->create();
        $plugin->type = PluginType::CustomProcessor->value;
        $plugin->save();

        $query = Plugin::query();
        Plugin::scopeCustomProcessors($query);
        $query->where('id', $plugin->id);
        $this->assertCount(1, $query->get());
    }

    public function test_plugin_get_scope_external() {
        /* @var Plugin $plugin */
        /* @var Builder $query */
        $plugin = Plugin::factory()->create();
        $plugin->source = PluginSource::Internal->value;
        $plugin->save();

        $query = Plugin::query();
        Plugin::scopeExternal($query);
        $query->where('id', $plugin->id);
        $this->assertCount(0, $query->get());

        $plugin = Plugin::factory()->create();
        $plugin->source = PluginSource::External->value;
        $plugin->save();

        $query = Plugin::query();
        Plugin::scopeExternal($query);
        $query->where('id', $plugin->id);
        $this->assertCount(1, $query->get());
    }

    public function test_plugin_get_scope_internal() {
        /* @var Plugin $plugin */
        /* @var Builder $query */
        $plugin = Plugin::factory()->create();
        $plugin->source = PluginSource::External->value;
        $plugin->save();

        $query = Plugin::query();
        Plugin::scopeInternal($query);
        $query->where('id', $plugin->id);
        $this->assertCount(0, $query->get());

        $plugin = Plugin::factory()->create();
        $plugin->source = PluginSource::Internal->value;
        $plugin->save();

        $query = Plugin::query();
        Plugin::scopeInternal($query);
        $query->where('id', $plugin->id);
        $this->assertCount(1, $query->get());
    }

    public function test_plugin_get_scope_Enabled() {
        /* @var Plugin $plugin */
        /* @var Builder $query */
        $plugin = Plugin::factory()->create();
        $plugin->enabled = false;
        $plugin->save();

        $query = Plugin::query();
        Plugin::scopeEnabled($query);
        $query->where('id', $plugin->id);
        $this->assertCount(0, $query->get());

        $plugin = Plugin::factory()->create();
        $plugin->enabled = true;
        $plugin->save();

        $query = Plugin::query();
        Plugin::scopeEnabled($query);
        $query->where('id', $plugin->id);
        $this->assertCount(1, $query->get());
    }
}
