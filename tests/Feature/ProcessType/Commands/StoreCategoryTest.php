<?php

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\StoreCategory;
use Database\Builder\Definition\CategoryBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class StoreCategoryTest
 * @package Tests\Feature\ProcessType\Commands
 */
class StoreCategoryTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $category = app(CategoryBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = $category->toArray();

        $this->assertCount(0, $definition->categories);
        $definition = (new StoreCategory($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->categories);
    }
}
