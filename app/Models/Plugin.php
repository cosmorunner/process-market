<?php

namespace App\Models;

use App\Enums\PluginSource;
use App\Enums\PluginType;
use App\Interfaces\Versionable;
use App\Traits\UsesVersions;
use Database\Factories\PluginFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Plugin
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $source
 * @property string $creator_id
 * @property string $namespace
 * @property string $identifier
 * @property bool $enabled
 * @property string $latest_version
 * @property string|null $author_id
 * @property string|null $author_type
 * @property array $data
 * @property string|null $latest_version_id
 * @property string|null $latest_published_version_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|Organisation|null $author
 * @property-read Collection|PluginVersion[] $versions
 * @property-read PluginVersion $latestVersion
 * @property-read PluginVersion $latestPublishedVersion
 * @method static PluginFactory factory(...$parameters)
 * @method static Builder|Plugin enabled()
 * @method static Builder|Plugin external()
 * @method static Builder|Plugin internal()
 * @method static Builder|Plugin customProcessors()
 * @method static Builder|Plugin actionTypeComponent()
 * @method static Builder|Plugin newModelQuery()
 * @method static Builder|Plugin newQuery()
 * @method static Builder|Plugin query()
 * @method static Builder|Plugin whereAuthorId($value)
 * @method static Builder|Plugin whereAuthorType($value)
 * @method static Builder|Plugin whereCreatedAt($value)
 * @method static Builder|Plugin whereCreatorId($value)
 * @method static Builder|Plugin whereId($value)
 * @method static Builder|Plugin whereLatestPublishedVersionId($value)
 * @method static Builder|Plugin whereLatestVersionId($value)
 * @method static Builder|Plugin whereLatestVersion($value)
 * @method static Builder|Plugin whereName($value)
 * @method static Builder|Plugin whereNamespace($value)
 * @method static Builder|Plugin create($value)
 * @mixin Eloquent
 */
class Plugin extends Model implements Versionable {

    use HasFactory, HasUuids, UsesVersions;

    /**
     * Model property type-casting
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean',
        'data' => 'array'
    ];

    protected $guarded = [];

    /**
     * User who originally created the plugin.
     * @return BelongsTo
     */
    public function creator() {
        return $this->belongsTo(User::class);
    }

    /**
     * Author of the plugin. A user or organization.
     * @return MorphTo|null
     */
    public function author(): MorphTo|null {
        return $this->morphTo();
    }

    /**
     * Flag whether the plugin belongs to an organization.
     * @return bool
     */
    public function authoredByOrganisation(): bool {
        return $this->author_type === Organisation::class;
    }

    /**
     * Flag whether the plugin belongs to a user.
     * @return bool
     */
    public function authoredByUser(): bool {
        return $this->author_type === User::class;
    }

    /**
     * Returns the namespace with identifier without version.
     * @return Attribute
     */
    protected function fullNamespace(): Attribute {
        return Attribute::make(get: fn(mixed $value, array $attributes) => $attributes['namespace'] . '/' . $attributes['identifier']);
    }

    /**
     * Flag if the plugin in an action type component plugin.
     * @return bool
     */
    public function isActionTypeComponentPlugin(): bool {
        return PluginType::ActionTypeComponent->value === $this->type;
    }

    /**
     * Flag if the plugin in a status type plugin.
     * @return bool
     */
    public function isStatusTypePlugin(): bool {
        return PluginType::StatusType->value === $this->type;
    }

    /**
     * Flag if the plugin in a custom processor plugin
     * @return bool
     */
    public function isCustomProcessorPlugin(): bool {
        return PluginType::CustomProcessor->value === $this->type;
    }

    /**
     * @param Builder $query
     * @return void
     */
    public static function scopeActionTypeComponent($query) {
        $query->where('type', PluginType::ActionTypeComponent->value);
    }

    /**
     * @param Builder $query
     * @return void
     */
    public static function scopeCustomProcessors($query) {
        $query->where('type', PluginType::CustomProcessor->value);
    }

    /**
     * @param Builder $query
     * @return void
     */
    public static function scopeExternal($query) {
        $query->where('source', PluginSource::External->value);
    }

    /**
     * @param Builder $query
     * @return void
     */
    public static function scopeInternal($query) {
        $query->where('source', PluginSource::Internal->value);
    }

    /**
     * @param Builder $query
     * @return void
     */
    public static function scopeEnabled($query) {
        $query->where('enabled', true);
    }
}