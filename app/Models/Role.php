<?php /** @noinspection PhpUnused */

namespace App\Models;

use App\Interfaces\Accessible;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Role
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $owner_id
 * @property string|null $owner_type
 * @property bool $is_admin
 * @property bool $is_owner
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $locked
 * @property-read Model|Eloquent|Accessible $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|Access[] $accesses
 * @property-read \Illuminate\Database\Eloquent\Collection|Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|Invitation[] $invitations
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $users
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereDescription($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereIsAdmin($value)
 * @method static Builder|Role whereLocked($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereOwnerId($value)
 * @method static Builder|Role whereOwnerType($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @method static Builder|Role create($value)
 * @mixin Eloquent
 */
class Role extends Model {

    use HasUuids, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'locked' => 'bool',
        'is_admin' => 'bool'
    ];

    protected $with = [];

    /**
     * The owner of the role.
     * @return MorphTo|null
     */
    public function owner() {
        return $this->morphTo();
    }

    /**
     * @return BelongsToMany
     */
    public function accesses() {
        return $this->belongsToMany(Access::class, 'roles', 'id', 'id', null, 'role_id');
    }

    /**
     * All permissions of the role.
     * @return BelongsToMany
     */
    public function permissions() {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    /**
     * Adds a permission to a role.
     * @param Permission $permission
     */
    public function addPermission(Permission $permission) {
        $this->permissions()->save($permission);
    }

    /**
     * Remove a permission from the role.
     * @param Permission $permission
     * @return int
     */
    public function removePermission(Permission $permission) {
        return $this->permissions()->detach($permission);
    }

    /**
     * Checks whether the role can exercise a certain right.
     * @param string $permissionIdent
     * @return bool
     */
    public function can(string $permissionIdent) {
        return $this->permissions->where('ident', '=', $permissionIdent)->isNotEmpty();
    }

    /**
     * Checks whether the role can exercise one of the specified rights.
     * @param Collection $permissionIdents
     * @return bool
     */
    public function canAny(Collection $permissionIdents) {
        return $this->permissions->pluck('ident')->intersect($permissionIdents)->count() > 0;
    }

    /**
     * Flag if the role is an administrative role.
     * @return bool
     */
    public function isAdmin() : bool {
        return $this->is_admin;
    }

    /**
     * Flag if the role is the owner role.
     * @return bool
     */
    public function isOwner() : bool {
        return $this->is_owner;
    }
}