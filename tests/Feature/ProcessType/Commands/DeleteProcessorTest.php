<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteProcessor;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ProcessorBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteProcessorTest.php
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteProcessorTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $processor = app(ProcessorBuilder::class)->make();
        $actionType = app(ActionTypeBuilder::class)->withProcessors([$processor])->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'id' => $actionType->processors->first()->id,
            'action_type_id' => $actionType->id,
        ];

        $this->assertCount(1, $actionType->processors);

        (new DeleteProcessor($payload, $definition, $processVersion))->execute();

        $this->assertCount(0, $definition->actionTypes->first()->processors);
    }

}
