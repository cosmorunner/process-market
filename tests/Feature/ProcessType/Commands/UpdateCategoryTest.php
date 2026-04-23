<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateCategory;
use Database\Builder\Definition\CategoryBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateCategoryTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateCategoryTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $category = app(CategoryBuilder::class)->make(['name' => 'foobar']);
        $definition = app(DefinitionBuilder::class)->withCategories([$category])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEquals('foobar', $definition->categories->first()->name);

        $updatedCategory = array_merge($definition->categories->first()->toArray(), ['name' => 'new foobar']);
        $definition = (new UpdateCategory($updatedCategory, $definition, $processVersion))->execute();

        $this->assertEquals('new foobar', $definition->categories->first()->name);
    }

}
