<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StoreVariable;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreVariableTest
 * @package Tests\Unit\Environment\Commands
 */
class StoreVariableTest extends TestCase {

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
    }

    public function test_environment_store_variable_type_json() {
        $environment = Environment::factory()->emptyWithName('Standard')->make();

        $payload = [
            'identifier' => 'identifier',
            'type' => 'TYPE_JSON',
            'value' => 'value',
            'is_public' => false
        ];

        $this->assertCount(0, $environment->blueprint->variables);
        $environment = (new StoreVariable($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->variables);
    }

    public function test_environment_store_variable_type_document() {
        $environment = Environment::factory()->emptyWithName('Standard')->make();

        $payload = [
            'identifier' => 'identifier',
            'type' => 'TYPE_DOCUMENT',
            'value' => 'value',
            'is_public' => false
        ];

        $this->assertCount(0, $environment->blueprint->variables);
        $environment = (new StoreVariable($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->variables);
    }

    public function test_environment_store_variable_type_smart_variable() {
        $environment = Environment::factory()->emptyWithName('Standard')->make();

        $payload = [
            'identifier' => 'identifier',
            'type' => 'TYPE_SMART_VARIABLE',
            'value' => 'value',
            'is_public' => false
        ];

        $this->assertCount(0, $environment->blueprint->variables);
        $environment = (new StoreVariable($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->variables);
    }
}
