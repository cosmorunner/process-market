<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\Models\Template;
use App\ProcessType\Commands\StoreTemplate;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreTemplateTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreTemplateTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $template = Template::first();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'template' => $template->id,
            'name' => 'Test'
        ];

        $this->assertCount(0, $definition->templates);
        $definition = (new StoreTemplate($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->templates);
    }

}
