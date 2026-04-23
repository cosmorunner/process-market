<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteState;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\StateBuilder;
use Database\Builder\Definition\StatusTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteStateTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteStateTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $state = app(StateBuilder::class)->make();
        $statusType = app(StatusTypeBuilder::class)->withStates([$state])->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'status_type_id' => $statusType->id,
            'state_id' => $statusType->states->first()->id
        ];

        $this->assertCount(1, $definition->statusType($statusType->id)->states);
        $definition = (new DeleteState($payload, $definition, $processVersion))->execute();
        $this->assertCount(0, $definition->statusType($statusType->id)->states);
    }

}
