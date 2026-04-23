<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteCategory;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\CategoryBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteCategoryTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteCategoryTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $category = app(CategoryBuilder::class)->make();
        $systemCategory = app(CategoryBuilder::class)->ofSystemType()->make();
        $actionTypeOfCategory = app(ActionTypeBuilder::class)->ofCategory($category)->make();

        $this->assertEquals($actionTypeOfCategory->category_id, $category->id);

        $definition = app(DefinitionBuilder::class)
            ->withActionTypes([$actionTypeOfCategory])
            ->withCategories([$category, $systemCategory])
            ->make();

        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = [
            'id' => $category->id,
        ];

        $this->assertCount(2, $definition->categories);
        $definition = (new DeleteCategory($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->categories);

        // Durch das Löschen der Kategorie muss nun auch die Kategorie auch die System-Kategorie gewechselt worden sein.
        $actionTypeOfCategory = $definition->actionType($actionTypeOfCategory->id);
        $this->assertEquals($systemCategory->id, $actionTypeOfCategory->category_id);
    }

}
