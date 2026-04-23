<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteStatusRule;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\StatusRuleBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteStatusRuleTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteStatusRuleTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $statusRules = app(StatusRuleBuilder::class)->make();
        $actionType = app(ActionTypeBuilder::class)->withStatusRules([$statusRules])->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'action_type_id' => $actionType->id,
            'status_type_id' => $actionType->statusRules->first()->status_type_id
        ];

        $this->assertCount(1, $actionType->statusRules);

        (new DeleteStatusRule($payload, $definition, $processVersion))->execute();

        $this->assertCount(0, $definition->actionTypes->first()->statusRules);
    }

}
