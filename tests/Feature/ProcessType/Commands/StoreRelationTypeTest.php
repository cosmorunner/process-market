<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreRelationType;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\RelationTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreRelationTypeTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreRelationTypeTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $relationType = app(RelationTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = $relationType->toArray();

        $this->assertCount(0, $definition->relationTypes);
        $definition = (new StoreRelationType($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->relationTypes);
    }

}
