<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateStatusType;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\StatusTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateStatusTypeTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateStatusTypeTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $statusType = app(StatusTypeBuilder::class)->make(['name' => 'foobar']);
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $definition->statusTypes->first()->name);

        $updatedStatusType = array_merge($definition->statusTypes->first()->toArray(), ['name' => 'new foobar']);

        $definition = (new UpdateStatusType($updatedStatusType, $definition, $processVersion))->execute();

        $this->assertEquals('new foobar', $definition->statusTypes->first()->name);
    }

}
