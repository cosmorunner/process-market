<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\DeleteVariable;
use App\Environment\Commands\StoreVariable;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteVariableTest
 * @package Tests\Unit\Environment\Commands
 */
class DeleteVariableTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_store_variable_type_string() {
        $environment = Environment::factory()->emptyWithName('Standard')->make();

        $payload = [
            'identifier' => 'identifier',
            'type' => 'TYPE_STRING',
            'value' => 'value',
            'is_public' => false
        ];

        $this->assertCount(0, $environment->blueprint->variables);
        $environment = (new StoreVariable($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->variables);
        $payload['id'] = $environment->blueprint->variables[0]->id;
        $environment = (new DeleteVariable($payload, $environment))->execute();
        $this->assertCount(0, $environment->blueprint->variables);
    }
}
