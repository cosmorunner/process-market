<?php

namespace App\Interfaces;

use App\Http\Resources\Cache\ModelCache;

/**
 * Für Eloquent-Models. Legt fest, dass das Model eine gecachte Repräsentation hat.
 */
interface Cachable {

    /**
     * Gibt den Resource-Klassennamen zurück, der für das Model genutzt wird.
     * @return string
     */
    public function cacheClassName(): string;

    /**
     * Gibt die Cached-Model als Resource zurück.
     * @return ModelCache
     */
    public function cacheResource(): ModelCache;

    /**
     * Gibt den Model-Cache als Array zurück.
     * @return array
     */
    public function cache(): array;

    /**
     * Entfernt den Cache für das Model.
     * @return void
     */
    public function flushCache(): void;
}
