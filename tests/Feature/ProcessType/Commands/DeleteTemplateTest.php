<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteTemplate;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ProcessorBuilder;
use Database\Builder\Definition\TemplateBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteTemplateTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteTemplateTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $template = app(TemplateBuilder::class)->make();
        $processor = app(ProcessorBuilder::class)->make([
            'identifier' => 'create_document',
            'options' => ['template' => pipe_notation($template)]
        ]);

        $actionTypeWithProcessor = app(ActionTypeBuilder::class)->withProcessors([$processor])->make();

        $this->assertEquals($actionTypeWithProcessor->processors->first()->options['template'], pipe_notation($template));

        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionTypeWithProcessor])->withTemplates([$template])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'id' => $template->id,
        ];

        $this->assertCount(1, $definition->templates);
        $definition = (new DeleteTemplate($payload, $definition, $processVersion))->execute();
        $this->assertCount(0, $definition->templates);

        // Durch das Löschen der Rolle muss nun auf die Rolle aus dem Prozessor entfernt worden sein.
        $actionTypeWithProcessor = $definition->actionType($actionTypeWithProcessor->id);
        $this->assertNull($actionTypeWithProcessor->processors->first()->options['template']);
    }

}
