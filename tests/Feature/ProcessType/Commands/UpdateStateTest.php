<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateState;
use App\ProcessType\StatusType;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\StateBuilder;
use Database\Builder\Definition\StatusTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateStateTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateStateTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_update_state_without_max_value() {
        $state = app(StateBuilder::class)->make();
        $statusType = app(StatusTypeBuilder::class)->withStates([$state])->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = $this->fullySetupProcessVersionWithEnvironment($definition);

        $payload = [
            'id' => $state->id,
            'description' => $state->description,
            'status_type_id' => $definition->statusTypes->first()->id,
            'min' => '200.000'
        ];

        $this->updateDefinition($processVersion, 'UpdateState', $payload)->assertOk()->assertJsonMissingValidationErrors([
                'min',
                'max'
            ]);

        $processVersion->refresh();
        /* @var StatusType $statusType */
        $statusType = $processVersion->definition->statusTypes->first();
        $this->assertEquals('200.000', $statusType->states->first()->min);
        $this->assertEquals('200.000', $statusType->states->first()->max);
    }

    public function test_commands_simple() {
        $state = app(StateBuilder::class)->make(['description' => 'foobar']);
        $statusType = app(StatusTypeBuilder::class)->withStates([$state])->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $definition->statusTypes->first()->states->first()->description);

        $updatedState = array_merge($definition->statusTypes->first()->states->first()->toArray(), ['max' => '5.000']);

        $definition = (new UpdateState($updatedState, $definition, $processVersion))->execute();

        $this->assertEquals('5.000', $definition->statusTypes->first()->states->first()->max);
    }

}
