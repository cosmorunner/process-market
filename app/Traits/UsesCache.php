<?php

namespace App\Traits;

use App\Http\Resources\Cache\ModelCache;
use App\Utils\RedisHelper;

/**
 * Gibt an, dass das Eloquent-Model gecached werden kann.
 */
trait UsesCache {

    /**
     * Gibt den Resource-Klassennamen zurück, der für das Model genutzt wird.
     * @return string
     */
    public function cacheClassName(): string {
        // z.B. App\Http\Resources\Cache\CachedGraph
        return 'App\\Http\\Resources\\Cache\\' . class_basename($this) . 'Cache';
    }

    /**
     * Gibt die Cached-Model als Resource zurück.
     * @return ModelCache
     */
    public function cacheResource(): ModelCache {
        $className = $this->cacheClassName();

        if (!class_exists($className)) {
            return new ModelCache($this);
        }

        return new $className($this);
    }

    /**
     * Returns the cached representation (resource) of the eloquent model.
     * If the cache does not exist, it will be created if Redis is enabled.
     * @return array
     */
    public function cache(): array {
        $resource = $this->cacheResource();

        // Fallback if there is no concrete cache class.
        if ($resource::class === ModelCache::class) {
            return $resource->toArray(request());
        }

        // When Redis is disabled, the array representation of the cached
        // of the cached model is returned.
        if (RedisHelper::isDisabled()) {
            return $resource->toArray(request());
        }

        if (RedisHelper::cacheExists($this)) {
            return RedisHelper::getMap($this);
        }

        // If the cachable model is not yet cached, it will be cached and then this
        // then this is returned.
        $array = $resource->toArray(request());

        RedisHelper::setMap($array, $this);

        return $array;
    }

    /**
     * Removes the cache for the model.
     * @return void
     */
    public function flushCache(): void {
        RedisHelper::flush($this);
    }

    /**
     * Regenerates the redis cache for the model.
     * @return void
     */
    public function regenerateCache(): void {
        $resource = $this->cacheResource();

        if (RedisHelper::isDisabled() || $resource::class === ModelCache::class) {
            return;
        }

        RedisHelper::setMap($resource->toArray(request()), $this);
    }
}
