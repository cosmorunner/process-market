<?php

namespace Tests\Unit\Models;

use App\Models\Organisation;
use App\Models\Setting;
use App\Models\User;
use App\Utils\RedisHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SettingTest
 * @package Tests\Unit\Models
 */
class SettingTest extends TestCase {

    use RefreshDatabase;

    public function test_setting_has_an_id() {
        $setting = Setting::factory()->create();
        $this->assertNotEmpty($setting->id);
    }

    public function test_setting_has_a_name() {
        $setting = Setting::factory()->create();
        $this->assertNotEmpty($setting->name);
    }

    public function test_setting_has_an_value() {
        $setting = Setting::factory()->create();
        $this->assertNotEmpty($setting->value);
    }

    public function test_setting_has_an_owner_id() {
        $setting = Setting::factory()->create();
        $this->assertNotEmpty($setting->owner_id);
    }

    public function test_setting_has_an_owner_type() {
        $setting = Setting::factory()->create();
        $this->assertEquals(User::class, $setting->owner_type);
    }

    public function test_setting_has_an_create_info() {
        $setting = Setting::factory()->create();
        $this->assertNotEmpty($setting->created_at);
    }

    public function test_setting_has_an_update_info() {
        $setting = Setting::factory()->create();
        $this->assertNotEmpty($setting->updated_at);
    }

    public function test_setting_has_an_owner() {
        $organisation = Organisation::factory()->create();
        $setting = Setting::factory()->ofUser($organisation)->create();
        $this->assertInstanceOf(Organisation::class, $setting->owner);
        $this->assertEquals($organisation->id, $setting->owner->id);
    }

    public function test_setting_can_retrieve_entry() {
        $setting = Setting::factory()->ofUser()->create();
        $value = Setting::retrieve($setting->name, owner: $setting->owner);
        $this->assertequals($value, $setting->value);
    }

    public function test_setting_can_retrieve_system_entry() {
        $setting = Setting::factory()->ofSystem()->create();
        $value = Setting::retrieveSystem($setting->name);
        $this->assertequals($value, $setting->value);
    }

    public function test_setting_can_retrieve_entry_from_database() {
        $setting = Setting::factory()->ofUser()->create();
        $value = Setting::retrieveUser($setting->name, user: $setting->owner);
        $this->assertequals($value, $setting->value);
    }

    public function test_setting_can_retrieve_settings_from_loggedin_user() {
        $user = User::Factory()->create();
        $setting = Setting::factory()->ofUser($user)->create();
        $value = Setting::retrieveUser($setting->name, user: $user);
        $this->assertequals($value, $setting->value);
    }

    public function test_setting_exists() {
        $setting = Setting::factory()->ofSystem()->create();
        $this->assertFalse(Setting::isPresent('notExistingSetting'));
        $this->assertTrue(Setting::isPresent($setting->name));

        $setting = Setting::factory()->ofUser()->create();
        $this->assertTrue(Setting::isPresent($setting->name, $setting->owner));
    }

    public function test_setting_exists_system() {
        $setting = Setting::factory()->ofSystem()->create();
        $this->assertFalse(Setting::existsSystem('notExistingSetting'));
        $this->assertTrue(Setting::existsSystem($setting->name));
    }

    public function test_setting_can_be_updated() {
        $user = User::Factory()->create();
        $setting = Setting::factory()->ofUser($user)->create(['value' => 'before']);
        $id = $setting->id;

        $this->assertequals('before', $setting->value);
        Setting::updateSetting($setting->name, 'after', $user);

        $setting = Setting::query()->where('id', '=', $setting->id)->first();
        $this->assertequals('after', $setting->value);
        $this->assertequals($id, $setting->id);
    }

    public function test_setting_multiple_can_be_updated() {
        $user = User::Factory()->create();
        $setting[] = Setting::factory()->ofUser($user)->create(['value' => 'first before']);
        $setting[] = Setting::factory()->ofUser($user)->create(['value' => 'second before']);
        $id[] = $setting[0]->id;
        $id[] = $setting[1]->id;

        $this->assertequals('first before', $setting[0]->value);
        $this->assertequals('second before', $setting[1]->value);
        Setting::updateSettings([$setting[0]->name => 'first after', $setting[1]->name => 'second after'], $user);

        $setting[0] = Setting::query()->where('id', '=', $setting[0]->id)->first();
        $setting[1] = Setting::query()->where('id', '=', $setting[1]->id)->first();
        $this->assertequals('first after', $setting[0]->value);
        $this->assertequals('second after', $setting[1]->value);
        $this->assertequals($id[0], $setting[0]->id);
        $this->assertequals($id[1], $setting[1]->id);
    }

    public function test_setting_can_be_deleted() {
        $setting = Setting::factory()->ofUser()->create();
        $this->assertNotnull(Setting::query()->where('id', '=', $setting->id)->first());
        Setting::deleteSetting($setting->name, $setting->owner);
        $this->assertNull(Setting::query()->where('id', '=', $setting->id)->first());
    }

    public function test_setting_multiple_can_be_deleted() {
        $owner = User::Factory()->create();
        $setting[] = Setting::factory()->ofUser($owner)->create();
        $setting[] = Setting::factory()->ofUser($owner)->create();
        $this->assertNotnull(Setting::query()->where('id', '=', $setting[0]->id)->first());
        $this->assertNotnull(Setting::query()->where('id', '=', $setting[1]->id)->first());
        Setting::deleteSettings([$setting[0]->name, $setting[1]->name], $owner);
        $this->assertNull(Setting::query()->where('id', '=', $setting[0]->id)->first());
        $this->assertNull(Setting::query()->where('id', '=', $setting[1]->id)->first());
    }

    public function test_setting_of_user_can_be_deleted() {
        $user = $this->login();
        $this->assertNotNull($user);
        $setting = Setting::factory()->ofUser($user)->create();
        $this->assertNotnull(Setting::query()->where('id', '=', $setting->id)->first());
        Setting::deleteUserSetting($setting->name);
        $this->assertNull(Setting::query()->where('id', '=', $setting->id)->first());
    }

    public function test_setting_can_be_inserted() {
        $owner = User::Factory()->create();
        $name = 'newSettingName';
        $value = 'newSettingValue';
        $this->assertNull(Setting::whereName($name)->first());

        Setting::insertSetting($name, $value, $owner);
        $this->assertInstanceOf(Setting::class, Setting::whereName($name)->first());
    }

    public function test_setting_can_be_upserted() {
        $owner = User::Factory()->create();
        $name = 'SettingName';
        $value = 'SettingValue';

        $this->assertNull(Setting::query()->where('name', '=', $name)->where('owner_id', '=', $owner->id)->first());

        Setting::upsertSetting($name, $value, $owner);
        $this->assertNotNull(Setting::query()->where('name', '=', $name)->where('owner_id', '=', $owner->id)->first());

        $name = 'newSettingName';
        $value = 'newSettingValue';
        $this->assertNull(Setting::query()->where('name', '=', $name)->where('owner_id', '=', $owner->id)->first());

        Setting::upsertSetting($name, $value, $owner);
        $setting = Setting::query()->where('name', '=', $name)->where('owner_id', '=', $owner->id)->first();

        $this->assertNotnull($setting);
    }

    public function test_settings_can_be_upserted() {
        $owner = User::Factory()->create();
        $name[] = 'first SettingName';
        $name[] = 'second SettingName';
        $value[] = 'first SettingValue';
        $value[] = 'second SettingValue';

        $this->assertNull(Setting::query()->where('name', '=', $name[0])->where('owner_id', '=', $owner->id)->first());
        $this->assertNull(Setting::query()->where('name', '=', $name[1])->where('owner_id', '=', $owner->id)->first());

        Setting::upsertSettings([$name[0] => $value[0], $name[1] => $value[1]], $owner);
        $this->assertNotNull(Setting::query()->where('name', '=', $name[0])->where('owner_id', '=', $owner->id)->first());
        $this->assertNotNull(Setting::query()->where('name', '=', $name[1])->where('owner_id', '=', $owner->id)->first());

        $name[0] = 'first newSettingName';
        $name[1] = 'second newSettingName';
        $value[0] = 'first newSettingValue';
        $value[1] = 'second newSettingValue';

        $this->assertNull(Setting::query()->where('name', '=', $name[0])->where('owner_id', '=', $owner->id)->first());
        $this->assertNull(Setting::query()->where('name', '=', $name[1])->where('owner_id', '=', $owner->id)->first());

        Setting::upsertSettings([$name[0] => $value[0], $name[1] => $value[1]], $owner);
        $this->assertNotNull(Setting::query()->where('name', '=', $name[0])->where('owner_id', '=', $owner->id)->first());
        $this->assertNotNull(Setting::query()->where('name', '=', $name[1])->where('owner_id', '=', $owner->id)->first());
    }

    public function test_setting_can_get_all_system_settings() {
        $setting[] = Setting::factory()->ofSystem()->create();
        $setting[] = Setting::factory()->ofUser()->create();

        $allSettings = Setting::system();

        $this->assertArrayHasKey($setting[0]->name, $allSettings);
        $this->assertArrayNotHasKey($setting[1]->name, $allSettings);
    }

    public function test_setting_of_owner() {
        $owner = Organisation::Factory()->create();
        $setting[] = Setting::factory()->ofUser($owner)->create();
        $setting[] = Setting::factory()->ofSystem()->create();

        $allSettings = Setting::ofOwner($owner);

        $this->assertArrayHasKey($setting[0]->name, $allSettings);
        $this->assertArrayNotHasKey($setting[1]->name, $allSettings);
    }

    public function test_setting_of_user() {
        $owner = User::Factory()->create();
        $setting[] = Setting::factory()->ofUser($owner)->create();
        $setting[] = Setting::factory()->ofSystem()->create();

        $allSettings = Setting::ofUser($owner);
        $this->assertArrayHasKey($setting[0]->name, $allSettings);
        $this->assertArrayNotHasKey($setting[1]->name, $allSettings);

        $allSettings = Setting::ofUser();
        $this->assertempty($allSettings);
    }

    public function test_setting_cache_system_settings() {
        Config::set('app.redis_enabled', true);
        RedisHelper::flushAll();

        $systemSetting = Setting::factory()->ofSystem()->create();
        $userSetting = Setting::factory()->ofUser()->create();

        $this->assertEmpty(RedisHelper::systemSettings());

        Setting::cacheSystemSettings();
        $cache = RedisHelper::systemSettings();

        $this->assertArrayHasKey($systemSetting->name, $cache);
        $this->assertArrayNotHasKey($userSetting->name, $cache);
    }

    public function test_setting_cache_user_settings() {
        Config::set('app.redis_enabled', true);
        RedisHelper::flushAll();

        $user = $this->login();
        $this->assertEmpty(RedisHelper::systemSettings());

        $userSetting = Setting::factory()->ofUser($user)->create();
        $otherUserSetting = Setting::factory()->ofUser()->create();
        $systemSetting = Setting::factory()->ofSystem()->create();

        Setting::cacheUserSettings();
        $cache = RedisHelper::userSettings($user);

        $this->assertArrayHasKey($userSetting->name, $cache);
        $this->assertArrayNotHasKey($otherUserSetting->name, $cache);
        $this->assertArrayNotHasKey($systemSetting->name, $cache);
    }

    public function test_setting_owner_user_setting() {
        Config::set('app.redis_enabled', true);
        RedisHelper::flushAll();

        $user = $this->login();
        $this->assertEmpty(RedisHelper::systemSettings());

        $userSetting = Setting::factory()->ofUser($user)->create();
        $otherUserSetting = Setting::factory()->ofUser()->create();
        $systemSetting = Setting::factory()->ofSystem()->create();

        Setting::cacheOwnerSettings($user);
        $cache = RedisHelper::userSettings($user);

        $this->assertArrayHasKey($userSetting->name, $cache);
        $this->assertArrayNotHasKey($otherUserSetting->name, $cache);
        $this->assertArrayNotHasKey($systemSetting->name, $cache);
    }
}