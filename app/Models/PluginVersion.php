<?php

namespace App\Models;

use Database\Factories\PluginVersionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PluginVersion
 * @property string $id
 * @property string $plugin_id
 * @property mixed $data
 * @property string $version
 * @property string|null $changelog
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Plugin|null $plugin
 * @method static PluginVersionFactory factory(...$parameters)
 * @method static Builder|PluginVersion newModelQuery()
 * @method static Builder|PluginVersion newQuery()
 * @method static Builder|PluginVersion query()
 * @method static Builder|PluginVersion whereChangelog($value)
 * @method static Builder|PluginVersion whereCreatedAt($value)
 * @method static Builder|PluginVersion whereData($value)
 * @method static Builder|PluginVersion whereId($value)
 * @method static Builder|PluginVersion wherePublishedAt($value)
 * @method static Builder|PluginVersion wherePluginId($value)
 * @method static Builder|PluginVersion whereUpdatedAt($value)
 * @method static Builder|PluginVersion whereVersion($value)
 * @method static Builder|PluginVersion create($value)
 * @mixin Eloquent
 */
class PluginVersion extends Model {

    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'published_at' => 'date'
    ];

    /**
     * @return BelongsTo
     */
    public function plugin() {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * Flag whether the version has already been completed.
     * @return bool
     */
    public function isPublished() {
        return $this->published_at !== null;
    }

    /**
     * Returns the complete namespace with version.
     * @return Attribute
     */
    protected function fullNamespace(): Attribute {
        return Attribute::make(get: fn(mixed $value, array $attributes) => $this->plugin->full_namespace . '@' . $attributes['version']);
    }


}