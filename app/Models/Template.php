<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Template
 * @package App\Models
 * @method static Builder|Template newModelQuery()
 * @method static Builder|Template newQuery()
 * @method static Builder|Template query()
 * @mixin Eloquent
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $data
 * @property string $preview
 * @property string $file
 * @property string $type
 * @property array $mapping
 * @property Carbon|null $created_at
 * @method static Builder|Template whereCreatedAt($value)
 * @method static Builder|Template whereData($value)
 * @method static Builder|Template whereDescription($value)
 * @method static Builder|Template whereFile($value)
 * @method static Builder|Template whereId($value)
 * @method static Builder|Template whereMapping($value)
 * @method static Builder|Template whereName($value)
 * @method static Builder|Template wherePreview($value)
 * @method static Builder|Template whereType($value)
 *  @method static Builder|Template create($value)
 */
class Template extends Model {

    const UPDATED_AT = null;

    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'mapping' => 'array'
    ];

}
