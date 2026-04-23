<?php

namespace Tests\Unit\Environment;

use App\Environment\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class UserTest
 * @package Tests\Unit\Environment
 */
class UserTest extends TestCase {

    use RefreshDatabase;

    public function test_enrivonment_system_task() {
        $firstName = 'jane';
        $lastName = 'doe';
        $options = [
            'id' => Str::uuid()->toString(),
            'identity_process_type' => '',
            'identity_process_instance' => '',
            'first_name' => $lastName,
            'last_name' => $firstName,
            'aliases' => [],
            'tags' => [],
            'locale' => 'de',
            'email' => Str::snake(substr($firstName, 0, 2)) . '.' . Str::snake(substr($lastName, 0, 8)) . '@example.com',
            'email_verified_at' => Carbon::now(),
            'password' => '$2y$10$KF8Jr70O7oMmH1CejqZ2TutmcLFeAu/YhDdhVKO.ad8C/kXoJSbjm',
            'remember_token' => Str::random(10),
        ];

        $blueprint = User::make($options)->toArray();

        $this->assertEquals($blueprint['id'], $options['id']);
        $this->assertEquals($blueprint['identity_process_type'], $options['identity_process_type']);
        $this->assertEquals($blueprint['identity_process_instance'], $options['identity_process_instance']);
        $this->assertEquals($blueprint['first_name'], $options['first_name']);
        $this->assertEquals($blueprint['last_name'], $options['last_name']);
        $this->assertEquals($blueprint['aliases'], $options['aliases']);
        $this->assertEquals($blueprint['tags'], $options['tags']);
        $this->assertEquals($blueprint['locale'], $options['locale']);
        $this->assertEquals($blueprint['email'], $options['email']);
        $this->assertEquals($blueprint['email_verified_at'], $options['email_verified_at']);
        $this->assertEquals($blueprint['password'], $options['password']);
    }
}
