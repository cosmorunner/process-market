<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteActionRule;
use Database\Builder\Definition\ActionRuleBuilder;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteActionRuleTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteActionRuleTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $actionRule = app(ActionRuleBuilder::class)->make();
        $actionType = app(ActionTypeBuilder::class)->withActionRules([$actionRule])->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'status_type_id' => $actionType->actionRules->first()->status_type_id
        ];

        $this->assertCount(1, $actionType->actionRules);

        (new DeleteActionRule($payload, $definition, $processVersion))->execute();

        $this->assertCount(0, $definition->actionTypes->first()->actionRules);
    }

}
