<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\DeleteUser;
use App\Environment\Commands\StoreUser;
use App\Environment\User;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteUserTest
 * @package Tests\Unit\Environment\Commands
 */
class DeleteUserTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_delete_user_simple() {
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

        $payload['id'] = $environment->blueprint->users->first()->id;

        $environment = (new DeleteUser($payload, $environment))->execute();
        $this->assertCount(0, $environment->blueprint->users);
    }
}
