<?php

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\StorePublicApi;
use App\Environment\Commands\StoreRelation;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class StoreRelationTest
 * @package Tests\Unit\Environment\Commands
 */
class StoreRelationTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_store_relation() {
        $environment = Environment::factory()->make();
        $payload = [
            'data' => [],
            'id' => Str::uuid()->toString(),
            'left' => Str::uuid()->toString(),
            'right' => Str::uuid()->toString(),
            'relation_type' => 'relation',
            'relation_type_name' => 'relation',
        ];
        $this->assertCount(0, $environment->blueprint->relations);
        $environment = (new StoreRelation($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->relations);
    }
}
