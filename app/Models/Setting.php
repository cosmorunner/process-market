<?php /** @noinspection PhpUnused */

namespace App\Models;

use App\Utils\RedisHelper;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Setting
 * @property string $id
 * @property string $name
 * @property array|null $value
 * @property string|null $owner_id
 * @property string|null $owner_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $owner
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting query()
 * @method static Builder|Setting whereCreatedAt($value)
 * @method static Builder|Setting whereId($value)
 * @method static Builder|Setting whereName($value)
 * @method static Builder|Setting whereOwnerId($value)
 * @method static Builder|Setting whereOwnerType($value)
 * @method static Builder|Setting whereUpdatedAt($value)
 * @method static Builder|Setting whereValue($value)
 * @method static Builder|Setting create($value)
 * @mixin Eloquent
 */
class Setting extends Model {

    use HasUuids, HasFactory;

    protected $casts = [
        'value' => 'array'
    ];

    protected $guarded = [];

    /**
     * Die Entität, zu der diese Einstellung gehört.
     * @return MorphTo|null
     */
    public function owner() {
        return $this->morphTo();
    }

    /**
     * Gibt eine Einstellungs-Option zurück.
     * @param string $name
     * @param null $default
     * @param Model|string|null $owner Wenn kein "owner" angegenen, wird der eingeloggte Benutzer, bzw. "guest" gewählt.
     * @return mixed
     */
    public static function retrieve(string $name, $default = null, Model|string $owner = null) {
        if (RedisHelper::isDisabled() || !RedisHelper::settingExists($name, $owner)) {
            return self::retrieveFromDatabase($name, $default, $owner);
        }
        else {
            return self::retrieveFromRedis($name, $default, $owner);
        }
    }

    /**
     * Gibt eine System-Einstellung zurück
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public static function retrieveSystem(string $name, $default = null): mixed {
        return self::retrieve($name, $default, RedisHelper::SYSTEM);
    }

    /**
     * Falls Redis deaktiviert ist, wird das Setting-Model aus der Datenbank geholt.
     * @param string $name
     * @param null $default
     * @param Model|string|null $owner
     * @return mixed
     */
    private static function retrieveFromDatabase(string $name, mixed $default = null, Model|string $owner = null): mixed {
        /* @var Setting $setting */
        $ownerId = $owner instanceof Model ? $owner->id : null;
        $setting = Setting::whereName($name)->where('owner_id', '=', $ownerId)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Gibt einen Einstellungswert aus der Redis-Datenbank zurück.
     * @param string $name
     * @param mixed|null $default
     * @param Model|string|null $owner
     * @return mixed
     */
    private static function retrieveFromRedis(string $name, mixed $default = null, Model|string $owner = null): mixed {
        return RedisHelper::settings($owner)[$name] ?? $default;
    }

    /**
     * Gibt eine Einstellungs-Option des angemeldeten Benutzers zurück.
     * @param string $name
     * @param null $default
     * @param Model|null $user Optionale Angabe eines Benutzers, dessen Einstellungswert geholt werden soll.
     * Standardgemäß wird der eingeloggte Benutzer gewählt.
     * @return mixed
     */
    public static function retrieveUser(string $name, $default = null, Model $user = null) {
        /* @var $user User */
        $user = $user ?? auth()->user();

        return self::retrieve($name, $default, $user);
    }

    /**
     * Prüft, ob eine Einstellung existiert.
     * @param string $name
     * @param Model|string|null $owner
     * @return bool
     */
    public static function isPresent(string $name, Model|string $owner = null): bool {
        $ownerId = $owner instanceof Model ? $owner->id : null;

        return Setting::whereName($name)->where('owner_id', '=', $ownerId)->first() instanceof Setting;
    }

    /**
     * Prüft ob eine Systemeinstellung existiert.
     * @param string $name
     * @return bool
     */
    public static function existsSystem(string $name): bool {
        return self::isPresent($name, RedisHelper::SYSTEM);
    }

    /**
     * Aktualisiert eine Einstellung. Der SettingObserver regelt das Aktualisieren des Redis-Cache.
     * @param $name
     * @param $value
     * @param Model|null $owner
     */
    public static function updateSetting($name, $value, Model $owner = null) {
        $setting = self::whereName($name)->where('owner_id', '=', $owner?->id)->first();

        if ($setting instanceof Setting) {
            $setting->update([
                'value' => $value,
                'owner_id' => $owner?->id,
                'owner_type' => is_null($owner) ? null : $owner::class
            ]);
        }
    }

    /**
     * Mehrere Einstellungen aktualisieren.
     * @param array $settings
     * @param Model|null $owner
     */
    public static function updateSettings(array $settings, Model $owner = null) {
        foreach ($settings as $key => $value) {
            self::updateSetting($key, $value, $owner);
        }
    }

    /**
     * Löscht eine Einstellung.
     * @param $name
     * @param Model|null $owner
     */
    public static function deleteSetting($name, Model $owner = null) {
        Setting::whereName($name)->where('owner_id', '=', $owner?->id)->delete();

        // If no owner was passed during deletion, a system setting is deleted.
        if (is_null($owner)) {
            $owner = RedisHelper::SYSTEM;
        }

        $settings = RedisHelper::settings($owner);

        if (array_key_exists($name, $settings)) {
            unset($settings[$name]);
            RedisHelper::setField('settings', $settings, $owner);
        }
    }

    /**
     * Deletes a setting of the logged in user.
     * @param $name
     * @noinspection PhpUnused
     */
    public static function deleteUserSetting($name) {
        $user = $user ?? auth()->user();
        self::deleteSetting($name, $user);
    }

    /**
     * Mehrere Einstellungen löschen.
     * @param array $names Namen der Einstellungen
     * @param Model|null $owner
     */
    public static function deleteSettings(array $names, Model $owner = null) {
        foreach ($names as $name) {
            self::deleteSetting($name, $owner);
        }
    }

    /**
     * Erstellt eine Einstellung. Der SettingObserver regelt das Aktualisieren des Redis-Cache.
     * @param $name
     * @param $value
     * @param Model|null $owner
     */
    public static function insertSetting($name, $value, Model $owner = null) {
        if (!self::isPresent($name, $owner)) {
            self::create([
                'name' => $name,
                'value' => $value,
                'owner_id' => is_null($owner) ? null : $owner->id,
                'owner_type' => is_null($owner) ? null : $owner::class
            ]);
        }
    }

    /**
     * Aktualisiert oder erstellt eine Einstellung.
     * @param $name
     * @param $value
     * @param Model|null $owner
     */
    public static function upsertSetting($name, $value, Model $owner = null) {
        if (self::isPresent($name, $owner)) {
            self::updateSetting($name, $value, $owner);
        }
        else {
            self::insertSetting($name, $value, $owner);
        }

    }

    /**
     * @param $name
     * @param $value
     */
    public static function upsertSettingForUser($name, $value) {
        /* @var User $user */
        $user = auth()->user();
        self::upsertSetting($name, $value, $user);
    }

    /**
     * Mehrere Einstellungen aktualisieren.
     * @param array $settings
     * @param Model|null $owner
     */
    public static function upsertSettings(array $settings, Model $owner = null) {
        foreach ($settings as $key => $value) {
            self::upsertSetting($key, $value, $owner);
        }
    }

    /**
     * Gibt alle System-Settings-Werte als Array zurück.
     * @return array
     */
    public static function system() {
        if (RedisHelper::isDisabled() || !RedisHelper::cacheExists(RedisHelper::SYSTEM)) {
            $settings = Setting::whereOwnerId(null)->get();

            return $settings->mapWithKeys(fn($setting) => [$setting['name'] => $setting['value']])->toArray();
        }

        return RedisHelper::systemSettings();
    }

    /**
     * Gibt alle Einstellungen von einem Model zurück.
     * @param Model $owner
     * @return array|null
     */
    public static function ofOwner(Model $owner) {
        if (RedisHelper::isDisabled()) {
            $settings = Setting::whereOwnerId($owner->id)->get();

            return $settings->mapWithKeys(fn($setting) => [$setting['name'] => $setting['value']])->toArray();
        }

        $ownerSettings = RedisHelper::settings($owner);

        return empty($ownerSettings) ? Setting::cacheOwnerSettings($owner) : $ownerSettings;
    }

    /**
     * Gibt alle Benutzer-Einstellungen
     * @param User|null $user
     * @return array
     */
    public static function ofUser(User $user = null) {
        if (!RedisHelper::isEnabled()) {
            $user = $user ?? auth()->user();

            if (!$user instanceof User) {
                return [];
            }

            return Setting::whereOwnerId($user->id)
                ->get()
                ->mapWithKeys(fn(Setting $setting) => [$setting->name => $setting->value])
                ->toArray();
        }

        return RedisHelper::userSettings();
    }

    /**
     * Cache all system settings in redis.
     */
    public static function cacheSystemSettings() {
        $settings = Setting::whereOwnerId(null)->get();
        $asArray = $settings->mapWithKeys(fn(Setting $setting) => [$setting->name => $setting->value])->toArray();
        RedisHelper::setField('settings', $asArray, RedisHelper::SYSTEM);
    }

    /**
     * Cache user settings in redis.
     */
    public static function cacheUserSettings() {
        $userId = auth()->user()->id ?? null;
        $settings = Setting::whereOwnerId($userId)->where('owner_type', '=', User::class)->get();
        $asArray = $settings->mapWithKeys(fn(Setting $setting) => [$setting->name => $setting->value])->toArray();
        RedisHelper::setField('settings', $asArray);
    }

    /**
     * Caches the settings for a specific owner.
     * @param Model $owner
     * @return array
     */
    public static function cacheOwnerSettings(Model $owner): array {
        $settings = Setting::whereOwnerId($owner->id)->where('owner_type', '=', $owner::class)->get();
        $asArray = $settings->mapWithKeys(fn(Setting $setting) => [$setting->name => $setting->value])->toArray();
        RedisHelper::setField('settings', $asArray, $owner);

        return $asArray;
    }

}
