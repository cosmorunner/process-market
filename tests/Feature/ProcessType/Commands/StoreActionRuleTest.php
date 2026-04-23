<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreActionRule;
use Database\Builder\Definition\ActionRuleBuilder;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreActionRuleTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreActionRuleTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $actionType = app(ActionTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = app(ActionRuleBuilder::class)->make([
            'action_type_id' => $actionType->id
        ])->toArray();

        $this->assertCount(0, $definition->actionTypes->first()->actionRules);
        $definition = (new StoreActionRule($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->actionTypes->first()->actionRules);
    }

}
