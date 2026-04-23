<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateRelationType;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\RelationTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateRelationTypeTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateRelationTypeTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $relationType = app(RelationTypeBuilder::class)->make(['description' => 'foobar']);
        $definition = app(DefinitionBuilder::class)->withRelationTypes([$relationType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $definition->relationTypes->first()->description);

        $updatedRelationType = array_merge($definition->relationTypes->first()->toArray(), ['description' => 'new foobar']);

        $definition = (new UpdateRelationType($updatedRelationType, $definition, $processVersion))->execute();

        $this->assertEquals('new foobar', $definition->relationTypes->first()->description);
    }

}
