<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateTemplate;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\TemplateBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateTemplateTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateTemplateTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $template = app(TemplateBuilder::class)->make(['name' => 'foobar']);
        $definition = app(DefinitionBuilder::class)->withTemplates([$template])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $definition->templates->first()->name);

        $updatedTemplate = array_merge($definition->templates->first()->toArray(), ['name' => 'new foobar']);

        $definition = (new UpdateTemplate($updatedTemplate, $definition, $processVersion))->execute();

        $this->assertEquals('new foobar', $definition->templates->first()->name);
    }

}
