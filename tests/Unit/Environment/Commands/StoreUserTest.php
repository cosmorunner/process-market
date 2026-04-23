<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreUser;
use App\Environment\User;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class StoreUserTest
 * @package Tests\Unit\Environment\Command
 */
class StoreUserTest extends TestCase {

    use RefreshDatabase;

    public function test_store_user_simple() {
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
        $this->assertEquals('johndoe@example.com', $user->email);
        $this->assertEquals('alias1', $user->aliases[0]);
        $this->assertEquals('tag1', $user->tags[0]);
        $this->assertEquals('allisa/person@1.0.0', $user->identity_process_type);
    }

    public function test_store_user_rule_valid_payload() {
        $environment = $this->fullySetupEnvironment();

        $payload = [
            'id' => Str::uuid()->toString(),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'identity_process_type' => 'allisa/person@latest',
            'aliases' => ['alias1'],
            'tags' => ['tag1'],
        ];

        $this->updateEnvironmentBlueprint($environment->processVersion, $environment, 'StoreUser', $payload)->assertOk();
    }

    public function test_store_user_rule_missing_aliases() {
        $environment = $this->fullySetupEnvironment();

        $payload = [
            'id' => Str::uuid()->toString(),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'identity_process_type' => 'allisa/person@latest',
            'aliases' => []
        ];

        $this->updateEnvironmentBlueprint($environment->processVersion, $environment, 'StoreUser', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('aliases')
            ->decodeResponseJson()
            ->json();
    }

}
