<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreVariable;
use App\Environment\Commands\UpdateVariable;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateVariableTest
 * @package Tests\Unit\Environment\Commands
 */
class UpdateVariableTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_update_variable() {
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
        $this->assertEquals($payload['type'], $environment->blueprint->variables[0]->type);

        $payload['id'] = $environment->blueprint->variables->first()->id;
        $payload['type'] = 'TYPE_JSON';
        $environment = (new UpdateVariable($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->variables);
        $this->assertEquals($payload['type'], $environment->blueprint->variables[0]->type);
    }
}
