<?php

namespace Database\Factories;

use App\Models\Plugin;
use App\Models\PluginVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class PluginVersionFactory
 * @package Database\Factories
 */
class PluginVersionFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = PluginVersion::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        $plugin = Plugin::factory()->create();

        return [
            'id' => $this->faker->uuid,
            'plugin_id' =>$plugin->id,
            'data' => $plugin->data,
            'version' => "test-version",
            'changelog' => 'test-changelog',
            'published_at' => now()
        ];
    }

    /**
     * Plugin to which the version is linked.
     * @param Plugin|null $plugin
     * @return PluginVersionFactory
     */
    public function ofPlugin(Plugin $plugin = null) {
        if ($plugin === null){
            $plugin = Plugin::factory()->create();
        }

        return $this->afterCreating(function(PluginVersion $pluginVersion) use ($plugin) {
            $pluginVersion->plugin()->associate($plugin)->save();
        });
    }

    /**
     * Sets the version.
     * @param string $version
     * @return PluginVersionFactory
     */
    public function ofVersion(string $version) {
        return $this->state(function () use ($version) {
            return [
                'version' => $version
            ];
        });
    }
}