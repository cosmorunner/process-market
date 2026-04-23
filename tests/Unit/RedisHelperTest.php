<?php

namespace Tests\Unit;

use App\Models\User;
use App\Utils\RedisHelper;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class RedisHelperTest
 * @package Tests\Unit
 */
class RedisHelperTest extends TestCase {

    use RefreshDatabase;

    /**
     * We overwrite the setup method to enable redis cache for all tests
     * @return void
     * @throws Exception
     */
    public function setUp(): void {
        parent::setUp();

        Config::set('app.redis_enabled', true);
        RedisHelper::flushAll();
    }

    public function test_the_user_key_is_generated_for_an_authenticated_user() {
        $user = $this->login();
        $this->assertEquals('user:' . $user->id, RedisHelper::key());
    }

    public function test_the_user_key_is_generated_for_another_user_when_authenticated() {
        $this->login();
        $otherUser = User::factory()->create();
        $this->assertEquals('user:' . $otherUser->id, RedisHelper::key($otherUser));
    }

    public function test_the_guest_key_is_generated_when_authenticated() {
        $this->assertEquals('guest', RedisHelper::key());
    }

    public function test_the_system_key_is_generated_for_system_when_authenticated() {
        $this->login();
        $this->assertEquals(RedisHelper::SYSTEM, RedisHelper::key(RedisHelper::SYSTEM));
    }

    public function test_the_system_key_is_generated_for_system_when_unauthenticated() {
        $this->assertEquals(RedisHelper::SYSTEM, RedisHelper::key(RedisHelper::SYSTEM));
    }

    public function test_it_can_set_a_field_for_system_when_unauthenticated() {
        $this->assertFalse(RedisHelper::cacheExists(RedisHelper::SYSTEM));

        RedisHelper::setField('foo', 'bar', RedisHelper::SYSTEM);

        $this->assertTrue(RedisHelper::cacheExists(RedisHelper::SYSTEM));
        $this->assertTrue(RedisHelper::fieldExists('foo', RedisHelper::SYSTEM));
    }

    public function test_it_can_set_a_field_for_system_when_authenticated() {
        $this->login();

        $this->assertFalse(RedisHelper::cacheExists(RedisHelper::SYSTEM));

        RedisHelper::setField('foo', 'bar', RedisHelper::SYSTEM);

        $this->assertTrue(RedisHelper::cacheExists(RedisHelper::SYSTEM));
        $this->assertTrue(RedisHelper::fieldExists('foo', RedisHelper::SYSTEM));
    }

    public function test_it_can_set_a_field_for_an_authenticated_user() {
        $this->login();

        $this->assertFalse(RedisHelper::cacheExists());

        RedisHelper::setField('foo', 'bar');

        $this->assertTrue(RedisHelper::cacheExists());
        $this->assertTrue(RedisHelper::fieldExists('foo'));
    }

    public function test_it_can_set_an_array_value_for_a_field_for_an_authenticated_user() {
        $this->login();

        $this->assertFalse(RedisHelper::cacheExists());

        RedisHelper::setField('foo', ['test_1' => 'value_1']);

        $this->assertTrue(RedisHelper::cacheExists());
        $this->assertTrue(RedisHelper::fieldExists('foo'));
    }

    public function test_it_can_set_a_collection_value_for_a_field_for_an_authenticated_user() {
        $this->login();

        $this->assertFalse(RedisHelper::cacheExists());

        RedisHelper::setField('foo', collect(['test_1' => 'value_1']));

        $this->assertTrue(RedisHelper::cacheExists());
        $this->assertTrue(RedisHelper::fieldExists('foo'));
    }

    public function test_it_can_set_a_field_for_guest_user() {
        $this->assertFalse(RedisHelper::cacheExists(RedisHelper::GUEST));

        // Hier wird der Scope nicht angegeben. RedisHelper erkennt, dass kein Benutzer eingeloggt ist und nimmt dadurch den Guest-Scope.
        RedisHelper::setField('foo', 'bar');

        $this->assertTrue(RedisHelper::cacheExists(RedisHelper::GUEST));
        $this->assertTrue(RedisHelper::fieldExists('foo', RedisHelper::GUEST));
    }

    public function test_it_can_get_a_field_for_system_when_unauthenticated() {
        RedisHelper::setField('foo', 'bar', RedisHelper::SYSTEM);

        $this->assertEquals('bar', RedisHelper::getField('foo', RedisHelper::SYSTEM));
    }

    public function test_it_can_get_a_field_for_system_when_authenticated() {
        $this->login();

        RedisHelper::setField('foo', 'bar', RedisHelper::SYSTEM);

        $this->assertEquals('bar', RedisHelper::getField('foo', RedisHelper::SYSTEM));
    }

    public function test_it_can_get_a_field_for_an_authenticated_user() {
        $this->login();

        RedisHelper::setField('foo', 'bar');

        $this->assertEquals('bar', RedisHelper::getField('foo'));
    }

    public function test_it_can_get_an_array_value_for_a_field_for_an_authenticated_user() {
        $this->login();

        $value = ['test_1' => 'value_1'];

        RedisHelper::setField('foo', $value);

        $this->assertTrue(is_array(RedisHelper::getField('foo')));
        $this->assertEquals(json_encode($value), json_encode(RedisHelper::getField('foo')));
    }

    public function test_it_can_get_a_collection_value_for_a_field_for_an_authenticated_user() {
        $this->login();

        $value = ['test_1' => 'value_1'];

        RedisHelper::setField('foo', collect(['test_1' => 'value_1']));

        $this->assertTrue(is_array(RedisHelper::getField('foo')));
        $this->assertEquals(json_encode($value), json_encode(RedisHelper::getField('foo')));
    }

    public function test_it_can_get_a_field_for_guest_user() {
        $this->assertFalse(RedisHelper::cacheExists(RedisHelper::GUEST));

        // Hier wird der Scope nicht angegeben. RedisHelper erkennt, dass kein Benutzer eingeloggt ist und nimmt dadurch den Guest-Scope.
        RedisHelper::setField('foo', 'bar');

        $this->assertTrue(RedisHelper::cacheExists(RedisHelper::GUEST));
        $this->assertTrue(RedisHelper::fieldExists('foo', RedisHelper::GUEST));
    }

    public function test_it_can_delete_a_field_for_system() {
        $this->assertFalse(RedisHelper::cacheExists(RedisHelper::SYSTEM));

        RedisHelper::setField('foo', 'bar', RedisHelper::SYSTEM);
        RedisHelper::setField('foo1', 'bar1', RedisHelper::SYSTEM);

        $this->assertTrue(RedisHelper::cacheExists(RedisHelper::SYSTEM));
        $this->assertTrue(RedisHelper::fieldExists('foo', RedisHelper::SYSTEM));
        $this->assertTrue(RedisHelper::fieldExists('foo1', RedisHelper::SYSTEM));

        RedisHelper::delete('foo1', RedisHelper::SYSTEM);

        // "foo" muss noch existieren, "foo1" nicht.
        $this->assertTrue(RedisHelper::fieldExists('foo', RedisHelper::SYSTEM));
        $this->assertFalse(RedisHelper::fieldExists('foo1', RedisHelper::SYSTEM));
    }

    public function test_it_can_delete_the_cache_for_system() {
        $this->assertFalse(RedisHelper::cacheExists(RedisHelper::SYSTEM));

        RedisHelper::setField('foo', 'bar', RedisHelper::SYSTEM);
        RedisHelper::setField('foo1', 'bar1', RedisHelper::SYSTEM);

        $this->assertTrue(RedisHelper::cacheExists(RedisHelper::SYSTEM));

        RedisHelper::flush(RedisHelper::SYSTEM);

        $this->assertFalse(RedisHelper::cacheExists(RedisHelper::SYSTEM));
    }

    public function test_it_can_delete_a_field_for_an_authenticated_user() {
        $this->login();

        $this->assertFalse(RedisHelper::cacheExists());

        RedisHelper::setField('foo', 'bar');
        RedisHelper::setField('foo1', 'bar1');

        $this->assertTrue(RedisHelper::cacheExists());
        $this->assertTrue(RedisHelper::fieldExists('foo'));
        $this->assertTrue(RedisHelper::fieldExists('foo1'));

        RedisHelper::delete('foo1', auth()->user());

        // "foo" muss noch existieren, "foo1" nicht.
        $this->assertTrue(RedisHelper::fieldExists('foo'));
        $this->assertFalse(RedisHelper::fieldExists('foo1'));
    }

    public function test_it_can_delete_the_cache_for_an_authenticated_user() {
        $this->login();

        $this->assertFalse(RedisHelper::cacheExists());

        RedisHelper::setField('foo', 'bar');
        RedisHelper::setField('foo1', 'bar1');

        $this->assertTrue(RedisHelper::cacheExists());

        RedisHelper::flush(auth()->user());

        $this->assertFalse(RedisHelper::cacheExists());
    }

}
