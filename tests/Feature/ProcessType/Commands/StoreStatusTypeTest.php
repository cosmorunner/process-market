<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreStatusType;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\StatusTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreStatusTypeTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreStatusTypeTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_store_status_type_simple() {
        $statusType = app(StatusTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = $statusType->toArray();

        $this->assertCount(0, $definition->statusTypes);
        $definition = (new StoreStatusType($payload, $definition, $processVersion))->execute();
        $newStatusType = $definition->statusTypes->first();
        $state = $newStatusType->states->first();

        $this->assertCount(1, $definition->statusTypes);
        $this->assertCount(1, $newStatusType->states);
        $this->assertequals($newStatusType->default, $state->min);
        $this->assertequals($newStatusType->default, $state->max);
    }

    public function test_commands_store_status_type_can_store_smart_related_status() {
        $statusType = app(StatusTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = $statusType->toArray();

        $this->assertCount(0, $definition->statusTypes);
        $definition = (new StoreStatusType($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->statusTypes);
    }
}
