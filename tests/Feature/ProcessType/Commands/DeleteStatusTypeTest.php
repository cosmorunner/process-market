<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteStatusType;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\StatusTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteStatusTypeTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteStatusTypeTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $statusType = app(StatusTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'id' => $statusType->id
        ];

        $this->assertCount(1, $definition->statusTypes);
        $definition = (new DeleteStatusType($payload, $definition, $processVersion))->execute();
        $this->assertCount(0, $definition->statusTypes);
    }

}
