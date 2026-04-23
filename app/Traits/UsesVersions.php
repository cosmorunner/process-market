<?php

namespace App\Traits;

use App\Models\PluginVersion;
use App\Models\ProcessVersion;
use App\Models\SolutionVersion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Gives the entity methods to browse its versions.
 */
trait UsesVersions {

    /**
     * All versions of the entity.
     * @return HasMany
     */
    public function versions(): HasMany {
        return $this->hasMany($this::class . 'Version')->latest();
    }

    /**
     * The latest "In development" version.
     * @return BelongsTo
     */
    public function latestVersion(): BelongsTo {
        return $this->belongsTo($this::class . 'Version');
    }

    /**
     * The latest completed version.
     * @return BelongsTo
     * @noinspection PhpUnused
     */
    public function latestPublishedVersion(): BelongsTo {
        return $this->belongsTo($this::class . 'Version');
    }

    /**
     * All completed versions of the entity.
     */
    public function publishedVersions(string $version = null): Collection|Model|null {
        $publishedVersions = $this->versions->where('published_at', '!=', null);

        if (!$version) {
            return $publishedVersions;
        }

        return $this->version($version, true);
    }

    /**
     * Flag whether the entity already has a published version.
     * @return bool
     */
    public function hasPublishedVersion(): bool {
        return (bool) $this->latest_published_version_id;
    }

    /**
     * Returns a specific version of the entity.
     * @param string|null $version
     * @param bool $published Flag whether the version must be published.
     * With "latest" as the version the most current, published version is returned.
     * @return Model|ProcessVersion|SolutionVersion|PluginVersion|null
     */
    public function version(string|null $version, bool $published = null): Model|ProcessVersion|SolutionVersion|PluginVersion|null {
        if (is_null($version)) {
            $version = $this->latest_version;
        }

        // Filter by "published" flag.
        if (is_bool($published)) {
            $versions = $this->versions->filter(fn(Model $model) => $published ? $model->published_at : !$model->published_at);
        }
        else {
            $versions = $this->versions;
        }

        if ($version === 'latest') {
            return $versions->first(function (Model $versionModel) {
                return $this->latest_published_version_id === $versionModel->id;
            });
        }

        if ($version === 'develop') {
            return $versions->first(function (Model $versionModel) {
                return $this->latest_version_id === $versionModel->id;
            });
        }

        return $versions->first(fn(Model $versionModel) => $versionModel->version === $version);
    }
}
