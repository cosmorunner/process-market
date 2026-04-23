<?php

namespace Database\Factories;

use App\Enums\PluginSource;
use App\Enums\PluginType;
use App\Models\Organisation;
use App\Models\Plugin;
use App\Models\PluginVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * Class PluginFactory
 * @package Database\Factories
 */
class PluginFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     * @var string
     */
    protected $model = Plugin::class;

    /**
     * Define the model's default state.
     * @return array
     */
    public function definition() {
        return [
            'name' => $this->faker->words(3, true),
            'type' => PluginType::ActionTypeComponent->value,
            'source' => PluginSource::External->value,
            'creator_id' => $this->faker->uuid,
            'namespace' => 'allisa',
            'identifier' => 'new-plugin',
            'enabled' => true,
            'latest_version' => '0.0.1',
            'author_id' => $this->faker->uuid,
            'author_type' => User::class,
            'data' => ['test'],
            'latest_version_id' => $this->faker->uuid,
            'latest_published_version_id' => $this->faker->uuid,
        ];
    }

    /**
     * Creates the plugin with a receiver.
     * @param User|Organisation|null $author
     * @return PluginFactory
     */
    public function ofRecipient(User|Organisation $author = null) {
        if ($author === null){
            $author = User::factory()->create();
        }

        return $this->afterCreating(function(Plugin $plugin) use ($author) {
            $plugin->author()->associate($author)->save();
        });
    }

    /**
     * Creates the plugin with a version.
     * @param array|Collection|PluginVersion|null $versions
     * @return PluginFactory
     */
    public function withVersions(array|Collection|PluginVersion $versions = null) {
        if ($versions === null){
            $versions = [PluginVersion::factory()->create(), PluginVersion::factory()->create()];
        }
        else if ($versions instanceof PluginVersion){
            $versions = [$versions];
        }
        else {
            foreach ($versions as $version) {
                if (!$version instanceof PluginVersion) {
                    throw new InvalidArgumentException('All elements in the $versions ' . (is_array($versions) ? 'array' : $versions::class) . ' must  be instances of ' . PluginVersion::class);
                }
            }
        }

        return $this->afterCreating(function(Plugin $plugin) use ($versions) {
            $plugin->versions()->saveMany($versions);
        });
    }

    /**
     * Sets the author and owner of the plugin.
     * @param User $user
     * @return PluginFactory
     */
    public function ofCreatorAndAuthor(User $user) {
        return $this->state(function () use ($user) {
            return [
                'creator_id' => $user->id,
                'namespace' => $user->namespace,
                'author_type' => $user::class,
                'author_id' => $user->id
            ];
        });
    }
}