<?php

namespace App\Models;

use App\Interfaces\Licensable;
use App\Scopes\SolutionLicenseScope;
use Database\Factories\LicenseFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Lizenz für einen Lösung.
 * @property string $id
 * @property string $resource_type
 * @property string $resource_id
 * @property string $owner_type
 * @property string $owner_id
 * @property string $issuer_id
 * @property string $level
 * @property array $level_options
 * @property Carbon|null $created_at
 * @property-read User $issuer
 * @property-read Model|Eloquent $owner
 * @property-read Model|Eloquent|Licensable|Solution $resource
 * @method static LicenseFactory factory(...$parameters)
 * @method static Builder|License newModelQuery()
 * @method static Builder|License newQuery()
 * @method static Builder|License query()
 * @method static Builder|License whereCreatedAt($value)
 * @method static Builder|License whereId($value)
 * @method static Builder|License whereIssuerId($value)
 * @method static Builder|License whereLevel($value)
 * @method static Builder|License whereLevelOptions($value)
 * @method static Builder|License whereOwnerId($value)
 * @method static Builder|License whereOwnerType($value)
 * @method static Builder|License whereResourceId($value)
 * @method static Builder|License whereResourceType($value)
 * @method static Builder|License create($value)
 * @mixin Eloquent
 */
class SolutionLicense extends License {

    protected $table = 'licenses';

    /**
     * Query Scope auf Solution-Lizenzen.
     */
    protected static function booted() {
        static::addGlobalScope(new SolutionLicenseScope());
    }

    /**
     * Flagge, ob die Lizenz es erlaubt, dass der Eigentümer eine neue Prozess-Instanz
     * @noinspection PhpUnused
     */
    public function allowsCopy() {
        return $this->level === self::LEVEL_OPEN_SOURCE || $this->level_options['allow_copy'] ?? false;
    }

}
