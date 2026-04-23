<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateStatusTypes;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\StatusTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateStatusTypesTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateStatusTypesTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $statusType1 = app(StatusTypeBuilder::class)->make(['name' => 'foobar1']);
        $statusType2 = app(StatusTypeBuilder::class)->make(['name' => 'foobar2']);
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType1, $statusType2])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar1', $definition->statusTypes->first()->name);
        $this->assertEquals('foobar2', $definition->statusTypes->last()->name);

        $updatedStatusType1 = array_merge($definition->statusTypes->first()->toArray(), ['name' => 'new foobar1']);
        $updatedStatusType2 = array_merge($definition->statusTypes->last()->toArray(), ['name' => 'new foobar2']);

        $definition = (new UpdateStatusTypes(['items' => [$updatedStatusType1, $updatedStatusType2]], $definition, $processVersion))->execute();

        $this->assertEquals('new foobar1', $definition->statusTypes->first()->name);
        $this->assertEquals('new foobar2', $definition->statusTypes->last()->name);
    }

}
