<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Environment\Commands;

use App\Environment\Commands\DeleteRelation;
use App\Environment\Commands\StoreRelation;
use App\Models\Environment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class DeleteRelationTest
 * @package Tests\Unit\Environment\Commands
 */
class DeleteRelationTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_command_delete_relation() {
        $environment = Environment::factory()->make();

        $id = Str::uuid()->toString();
        $payload = [
            'data' => [],
            'id' => $id,
            'left' => Str::uuid()->toString(),
            'right' => Str::uuid()->toString(),
            'relation_type' => 'relation',
            'relation_type_name' => 'relation',
        ];
        $this->assertCount(0, $environment->blueprint->relations);
        $environment = (new StoreRelation($payload, $environment))->execute();
        $this->assertCount(1, $environment->blueprint->relations);
        $environment = (new DeleteRelation($payload, $environment))->execute();
        $this->assertCount(0, $environment->blueprint->relations);
    }
}
