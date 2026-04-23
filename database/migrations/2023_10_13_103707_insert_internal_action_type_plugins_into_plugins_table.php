<?php

use App\Enums\PluginSource;
use App\Enums\PluginType;
use App\Models\Plugin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;
use App\Models\PluginVersion;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        $plugins = config('plugins.internal.action_type_component');
        $creatorId = Str::uuid();

        foreach ($plugins as $pluginOptions) {
            $plugin = Plugin::create([
                'name' => $pluginOptions['name'],
                'type' => PluginType::ActionTypeComponent->value,
                'source' => PluginSource::Internal->value,
                'creator_id' => $creatorId,
                'namespace' => $pluginOptions['namespace'],
                'identifier' => $pluginOptions['identifier'],
                'enabled' => true,
                'latest_version' => '1.0.0',
                'data' => [
                    'icon' => $pluginOptions['icon']
                ]
            ]);

            $version = $plugin->versions()->save(PluginVersion::create([
                'plugin_id' => $plugin->id,
                'version' => '1.0.0',
                'changelog' => '',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'data' => [
                    'default_options' => $pluginOptions['default_options']
                ]
            ]));

            $plugin->update([
                'latest_version_id' => $version->id,
                'latest_published_version_id' => $version->id
            ]);
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Plugin::query()->internal()->actionTypeComponent()->delete();
    }
};
