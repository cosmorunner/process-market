<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Utils;

use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

/**
 * Helper class to interact with the redis db and user/system cache.
 * Class RedisHelper
 * @package App\Utils
 */
class RedisHelper {

    const SYSTEM = 'system';
    const GUEST = 'guest';

    /**
     * Returns a value from a hash map
     * By default, the user ID is selected as the key. For users who are not logged in, "system" is selected.
     * @param string $field
     * @param Model|string|null $scope Cached item from a model, system, guest or user.
     * @param mixed $default Default value or callable if Redis returns NULL.
     * @return mixed
     */
    public static function getField(string $field, Model|string $scope = null, mixed $default = null): mixed {
        if (self::isDisabled()) {
            return $default;
        }

        $value = Redis::hget(self::key($scope), $field);

        if (is_null($value) && is_callable($default)) {
            return $default();
        }
        else if (is_null($value)) {
            return $default;
        }

        // If the value is a JSON-encoded string, the string is decoded.
        if (str_starts_with($value, '{') || str_starts_with($value, '[')) {
            $json = json_decode($value, true);

            // If successfully decoded, return the json.
            return json_last_error() === JSON_ERROR_NONE ? $json : $value;
        }

        return $value;
    }

    /**
     * Returns a cached hash map. By default, the user cache is returned.
     * @param Model|string|null $scope
     * @param array $default
     * @return array
     * @noinspection PhpUnused
     */
    public static function getMap(string|Model $scope = null, $default = []) {
        $value = Redis::hgetall(self::key($scope));

        if (empty($value)) {
            return $default;
        }

        $array = [];

        foreach ($value as $key => $jsonString) {
            $array[$key] = json_decode($jsonString, true);
        }

        return $array;
    }

    /**
     * Sets the value for the system cache or for a user.
     * By default, the user ID is selected as the key. For users who are not logged in, "system" is selected.
     * @param string $field
     * @param $value
     * @param Model|string|null $scope
     */
    public static function setField(string $field, $value, Model|string $scope = null) {
        if (!config('app.redis_enabled')) {
            return;
        }

        if ($value instanceof Arrayable) {
            $value = json_encode($value->toArray());
        }

        if (is_array($value)) {
            $value = json_encode($value);
        }

        Redis::hset(self::key($scope), $field, $value);
    }

    /**
     * Sets the hash map for a model.
     * @param array|Collection $values $array
     * @param Model|string|null $owner Eigentümer des Caches
     */
    public static function setMap(Collection|array $values, Model|string $owner = null) {
        if (!config('app.redis_enabled')) {
            return;
        }

        if ($values instanceof Collection) {
            $values = json_encode($values->toArray());
        }

        $dictionary = [];

        foreach ($values as $key => $value) {
            $dictionary[$key] = json_encode($value);
        }

        Redis::hmset(self::key($owner), $dictionary);
    }

    /**
     * Flag indicating whether a key from a user's cache exists.
     * @param Model|string|null $scope
     * @return bool
     */
    public static function cacheExists(Model|string $scope = null): bool {
        return Redis::exists(self::key($scope));
    }

    /**
     * Flag whether a key exists. If a $field has been specified, a field is searched for in the hash map.
     * @param string $field Field in the hash map
     * @param Model|string|null $scope If no scope is specified, the logged-in user is used as the scope.
     * If no user is logged in, the "guest" scope is selected.
     * @return bool
     */
    public static function fieldExists(string $field, Model|string $scope = null): bool {
        return Redis::hexists(self::key($scope), $field);
    }

    /**
     * Checks whether a settings key exists in the Redis DB.
     * @param string $name
     * @param Model|string|null $owner
     * @return bool
     */
    public static function settingExists(string $name, Model|string $owner = null): bool {
        return array_key_exists($name, self::settings($owner));
    }

    /**
     * Returns all cached user settings.
     * @param User|null $user Optional transfer of a user. If NULL, the logged-in user is used.
     * @return array
     */
    public static function userSettings(User $user = null): array {
        $user = $user ?? auth()->user();

        if (!$user) {
            return [];
        }

        return self::getField('settings', null, []);
    }

    /**
     * Returns the settings of a model.
     * @param Model|string|null $owner
     * @return array
     */
    public static function settings(Model|string $owner = null): array {
        return self::getField('settings', $owner, []);
    }

    /**
     * Returns all cached settings from the system.
     * @return array
     */
    public static function systemSettings(): array {
        return self::getField('settings', self::SYSTEM, []);
    }

    /**
     * Deletes a field from a hash.
     * By default, the user ID is selected as the key. For users who are not logged in, "guest" is selected.
     * @param string $field
     * @param Model|string|null $scope Benutzer, System-Scope oder Guest-Scope
     */
    public static function delete(string $field, Model|string $scope = null) {
        if (self::isDisabled()) {
            return;
        }

        Redis::hdel(self::key($scope), [$field]);
    }

    /**
     * Deletes the cache for an owner or scope
     * @param Model|string $scope User, system or guest scope
     */
    public static function flush(Model|string $scope) {
        if (self::isDisabled()) {
            return;
        }

        Redis::del([self::key($scope)]);
    }

    /**
     * Deletes the cache for the logged in user.
     * @noinspection PhpUnused
     */
    public static function flushUser() {
        if (auth()->check() && self::isEnabled()) {
            Redis::del([self::key(auth()->user())]);
        }
    }

    /**
     * Clears the complete cache for the current environment.
     * @var string $keyPattern Redis key pattern
     */
    public static function flushAll(string $keyPattern = '*') {
        if (self::isDisabled()) {
            return;
        }

        $prefix = config('database.redis.options.prefix');

        // keys() returns all keys WITH prefix of the environment.
        $keys = Redis::keys($keyPattern);

        // These must all be removed here because "del" itself still appends the prefix.
        $withoutPrefix = array_map(fn($key) => substr($key, strlen($prefix)), $keys);

        if (!empty($withoutPrefix)) {
            Redis::del($withoutPrefix);
        }
    }

    /**
     * Flush the redis cache for all users. System and guest cache remains.
     * @return void
     * @noinspection PhpUnused
     */
    public static function flushAllUsers() {
        self::flushAll('user:*');
    }

    /**
     * Erzeugt den Key unter dem der Wert in Redis gespeichert wird.
     * @param Model|string|null $scope
     * @return string
     */
    public static function key(Model|string $scope = null) {
        // Es wurde explizit ein Benutzer angegeben, also den Benutzer-Scope verwenden.
        if ($scope instanceof Model) {
            $modelName = Str::lower(class_basename($scope));

            return $modelName . ':' . $scope->id;
        }

        // Es wurde explizit ein "string"-Scope angegeben, also diesen nutzen.
        if (is_string($scope)) {
            return $scope;
        }

        /* @var User $user */
        $user = auth()->user();

        // Eingeloggten Benutzer als Scope nutzen.
        if (is_null($scope) && $user instanceof User) {
            return 'user:' . $user->id;
        }

        // Kein Scope und kein eingeloggter Benutzer.
        return self::GUEST;
    }


    /**
     * Flagge ob Redis aktiviert ist.
     * @return bool
     */
    public static function isEnabled() {
        return (bool) config('app.redis_enabled', false);
    }

    /**
     * Flagge ob Redis deaktiviert ist.
     * @return bool
     */
    public static function isDisabled() {
        return !self::isEnabled();
    }
}
