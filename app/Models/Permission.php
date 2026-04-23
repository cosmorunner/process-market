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
 * Class Permission
 * @package App
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $ident
 * @property string $scope
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Role[] $roles
 * @method static Builder|Permission newModelQuery()
 * @method static Builder|Permission newQuery()
 * @method static Builder|Permission query()
 * @method static Builder|Permission whereActive($value)
 * @method static Builder|Permission whereCreatedAt($value)
 * @method static Builder|Permission whereDescription($value)
 * @method static Builder|Permission whereId($value)
 * @method static Builder|Permission whereIdent($value)
 * @method static Builder|Permission whereName($value)
 * @method static Builder|Permission whereUpdatedAt($value)
 * @method static Builder|Permission whereScope($value)
 * @method static Builder|Permission create($value)
 * @mixin Eloquent
 */
class Permission extends Model {

    use HasUuids, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'active' => 'bool',
    ];

    /**
     * @return BelongsToMany
     */
    public function roles() {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Aktiviert die Berechtigung.
     */
    public function activate() {
        $this->active = true;
        $this->save();
    }

    /**
     * Deaktiviert die Berechtigung.
     */
    public function deactivate() {
        $this->active = false;
        $this->save();
    }

    /**
     * Verknüpft die Berechtigung zu einer Rolle.
     * @param Role $role
     */
    public function addToRole(Role $role) {
        $this->roles()->save($role);
    }

    /**
     * Verknüpft die Berechtigung zu einer Rolle.
     * @param Role $role
     */
    public function removeFromRole(Role $role) {
        $this->roles()->detach($role);
    }

}

