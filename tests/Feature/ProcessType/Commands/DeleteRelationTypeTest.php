<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteRelationType;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\RelationTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteRelationTypeTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteRelationTypeTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $relationType = app(RelationTypeBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withRelationTypes([$relationType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'id' => $relationType->id,
        ];

        $this->assertCount(1, $definition->relationTypes);
        $definition = (new DeleteRelationType($payload, $definition, $processVersion))->execute();
        $this->assertCount(0, $definition->relationTypes);
    }

}
