<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteActionType;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ProcessorBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteActionTypeTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteActionTypeTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $actionTypeToDelete = app(ActionTypeBuilder::class)->make();
        $processor = app(ProcessorBuilder::class)->make([
            'identifier' => 'execute_action',
            'options' => ['action_type' => pipe_notation($actionTypeToDelete)]
        ]);

        $actionTypeWithProcessor = app(ActionTypeBuilder::class)->withProcessors([$processor])->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionTypeToDelete, $actionTypeWithProcessor])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'id' => $actionTypeToDelete->id,
        ];

        $this->assertCount(2, $definition->actionTypes);
        $definition = (new DeleteActionType($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->actionTypes);

        // Prüfen, dass der gelöschte Aktionstyp aus den Prozessor-Optionen von dem anderen Aktionstyp entfernt wurde.
        $actionTypeWithProcessor = $definition->actionType($actionTypeWithProcessor->id);
        $this->assertNull($actionTypeWithProcessor->processors->first()->options['action_type']);
    }

}
