<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreActionType;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\CategoryBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreActionTypeTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreActionTypeTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_store_action_type_simple() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $category = app(CategoryBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withCategories([$category])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = $actionType->toArray();

        $this->assertCount(0, $definition->actionTypes);
        $definition = (new StoreActionType($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->actionTypes);
    }

}