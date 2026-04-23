<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateProcessor;
use App\ProcessType\Processor;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ProcessorBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class UpdateProcessorTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateProcessorTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_processor_simple() {
        $processor = app(ProcessorBuilder::class)->make([
            'identifier' => 'update_process_meta',
            'options' => ['name' => 'foo']
        ]);
        $actionType = app(ActionTypeBuilder::class)->withProcessors([$processor])->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foo', $definition->actionTypes->first()->processors->first()->toArray()['options']['name']);

        $updatedProcessor = array_merge($definition->actionTypes->first()->processors->first()
            ->toArray(), ['options' => ['name' => 'new foo']]);

        $definition = (new UpdateProcessor($updatedProcessor, $definition, $processVersion))->execute();

        $this->assertEquals('new foo', $definition->actionTypes->first()->processors->first()->toArray()['options']['name']);
    }

    public function test_commands_processor_can_be_updated_when_having_max_amount_of_type() {
        $this->withoutExceptionHandling();
        $user = $this->login();
        $count = Processor::MAX_ITEMS['trigger_connector'];
        $processors = Collection::times($count, fn() => app(ProcessorBuilder::class)->make(['identifier' => 'trigger_connector']));

        $actionType = app(ActionTypeBuilder::class)->withProcessors($processors->toArray())->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $process = Process::factory()->ofCreatorAndAuthor($user)->create();
        $processVersion = ProcessVersion::factory()->ofProcess($process)->withDefinition($definition)->create();

        $this->updateDefinition($processVersion, 'UpdateProcessor', $actionType->processors->first()->toArray())->assertOk();
    }
}
