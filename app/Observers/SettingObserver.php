<?php

namespace App\Observers;

use App\Models\Setting;
use App\Utils\RedisHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SettingObserver
 * @package App\Observers
 */
class SettingObserver {

    /**
     * Wenn eine Einstellung erstellt wurde, wird der Cache aktualisiert.
     * @param Setting $setting
     * @return void
     */
    public function created(Setting $setting) {
        if (RedisHelper::isDisabled()) {
            return;
        }

        if ($setting->owner instanceof Model) {
            Setting::cacheOwnerSettings($setting->owner);
        }
        else {
            Setting::cacheSystemSettings();
        }
    }

    /**
     * Wenn eine Einstellung aktualisiert wurde, wird der Cache aktualisiert.
     * @param Setting $setting
     * @return void
     */
    public function updated(Setting $setting) {
        if (RedisHelper::isDisabled()) {
            return;
        }

        if ($setting->owner instanceof Model) {
            Setting::cacheOwnerSettings($setting->owner);
        }
        else {
            Setting::cacheSystemSettings();
        }
    }

    /**
     * Wenn eine Einstellung gelöscht wurde, wird der Cache aktualisiert.
     * @param Setting $setting
     * @return void
     */
    public function deleted(Setting $setting) {
        if (RedisHelper::isDisabled()) {
            return;
        }

        if ($setting->owner instanceof Model) {
            Setting::cacheOwnerSettings($setting->owner);
        }
        else {
            Setting::cacheSystemSettings();
        }
    }
}
