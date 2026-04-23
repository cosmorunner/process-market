<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreUser;
use App\Environment\Commands\UpdateUser;
use App\Environment\User;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateUserTest
 * @package Tests\Unit\Environment\Commands
 */
class UpdateUserTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_update_user_simple() {
        $environment = Environment::factory()->emptyWithName('Standard')->make();

        $payload = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'identity_process_type' => 'allisa/person@1.0.0',
            'aliases' => ['alias1'],
            'tags' => ['tag1'],
        ];

        $this->assertCount(0, $environment->blueprint->users);
        $environment = (new StoreUser($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->users);

        $user = $environment->blueprint->users->first();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->first_name);
        $this->assertEquals('Doe', $user->last_name);

        $payload['id'] = $user->id;
        $payload['first_name'] = 'Jane';
        $payload['email'] = 'janedoe@example.com';

        $environment = (new UpdateUser($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->users);

        $user = $environment->blueprint->users->first();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Jane', $user->first_name);
        $this->assertEquals('Doe', $user->last_name);
        $this->assertEquals('janedoe@example.com', $user->email);
    }
}
