<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Tag
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Process[] $processes
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|Tag query()
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereId($value)
 * @method static Builder|Tag whereName($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 *  @method static Builder|Tag create($value)
 * @mixin Eloquent
 * @property string $color
 * @method static Builder|Tag whereColor($value)
 * @property-read int|null $processes_count
 */
class Tag extends Model {

    use HasUuids, HasFactory;

    protected $guarded = [];

    /**
     * Alle Prozesse mit diesem Tag.
     * @return BelongsToMany
     */
    public function processes() {
        return $this->belongsToMany(Process::class)->withTimestamps();
    }

}
