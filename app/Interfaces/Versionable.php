<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Legt fest, dass die Entität versionierbar ist. Die Entität hat ein eigenes Model was jede Version repräsentiert.
 */
interface Versionable {

    /**
     * Gibt die Versionen der Entität zurück.
     * @return HasMany
     */
    public function versions(): HasMany;

    /**
     * Gibt die aktuellste "in der Entwicklung" Version zurück.
     * @return BelongsTo
     */
    public function latestVersion(): BelongsTo;

    /**
     * Gibt die aktuellste, fertiggestellte Version zurück.
     * @return BelongsTo
     */
    public function latestPublishedVersion(): BelongsTo;

    /**
     * Gibt alle fertiggestellten Versionen der Entität zurück.
     * @param string|null $version Eine bestimmte fertiggestellte Version zurückgeben.
     * @return Collection|Model|null
     */
    public function publishedVersions(string $version = null): Collection|Model|null;

    /**
     * Flagge, ob die Entität bereits eine publizierte Version hat.
     * @return bool
     */
    public function hasPublishedVersion(): bool;

}
