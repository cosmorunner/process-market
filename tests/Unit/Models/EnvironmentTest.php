<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Models;

use App\Environment\Blueprint;
use App\Environment\User as EnvironmentUser;
use App\Models\Environment;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\User;
use App\ProcessType\ActionType;
use Database\Builder\BlueprintBuilder;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class EnvironmentTest
 * @package Tests\Unit\Models
 */
class EnvironmentTest extends TestCase {

    use RefreshDatabase;

    public function test_environment_has_an_id() {
        $environment = Environment::factory()->create();
        $this->assertNotEmpty($environment->id);
    }

    public function test_environment_has_a_name() {
        $environment = Environment::factory()->create();
        $this->assertIsString($environment->name);
    }

    public function test_environment_has_a_description() {
        $environment = Environment::factory()->create();
        $this->assertIsString($environment->description);
    }

    public function test_environment_has_a_process_version_id() {
        $environment = Environment::factory()->create();
        $this->assertIsString($environment->process_version_id);
    }

    public function test_environment_has_a_initial_action_type_id() {
        $environment = Environment::factory()->create();
        $this->assertIsString($environment->initial_action_type_id);
    }

    public function test_environment_has_a_query_context() {
        $environment = Environment::factory()->create();
        $this->assertIsString($environment->query_context);
    }

    public function test_environment_is_not_default() {
        $environment = Environment::factory()->create();
        $this->assertFalse($environment->default);
    }

    public function test_environment_is_not_public() {
        $environment = Environment::factory()->create();
        $this->assertFalse($environment->public);
    }

    public function test_environment_has_a_blueprint() {
        $environment = Environment::factory()->create();
        $this->assertInstanceOf(Blueprint::class, $environment->blueprint);
    }

    public function test_environment_belongs_to_a_processversion() {
        $environment = Environment::factory()->withProcessVersion()->create();
        $this->assertInstanceOf(ProcessVersion::class, $environment->processVersion);
    }

    public function test_environment_can_get_blueprint_attribute() {
        $environment = Environment::factory()->create();
        $blueprint = $environment->blueprint;

        $this->assertInstanceOf(Blueprint::class, $environment->blueprint);
        $this->assertNotNull($blueprint);
    }

    public function test_environment_can_update_blueprint_attribute() {
        $environment = Environment::factory()->create();

        $this->assertNotNull($environment->blueprint);
        $this->assertEmpty($environment->blueprint->users);

        $environment->updateBlueprint('users', collect([EnvironmentUser::make()]));
        $this->assertNotEmpty($environment->blueprint->users);
    }

    public function test_environment_can_return_the_raw_blueprint() {
        $environment = Environment::factory()->create();
        $this->assertIsArray($environment->getRawBlueprint());
    }

    public function test_environment_get_inital_action_type() {
        $actionType = app(ActionTypeBuilder::class)->withProcessors([])->make();
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();

        $environment = Environment::factory()->withInitialActionType($actionType)->create();
        ProcessVersion::factory()->withDefinition($definition)->withEnvironments([$environment])->create();

        $this->assertInstanceOf(ActionType::class, $environment->initialActionType());
        $this->assertEquals($actionType->id, $environment->initial_action_type_id);
    }

    public function test_environment_can_be_a_default_environment() {
        $environment = Environment::factory()->create();
        $this->assertFalse($environment->isDefault());

        $environment = Environment::factory()->isDefault()->create();
        $this->assertTrue($environment->isDefault());
    }

    public function test_environment_can_export_the_process_types_used_in_the_blueprint() {
        $user = User::factory()->create(['namespace' => 'max']);
        $organisation = Organisation::factory()->create(['namespace' => 'company']);

        $demoProcess = Process::factory()->ofCreatorAndAuthor($user)->withLatestPublishedVersion()->create();
        $identityProcess = Process::factory()->ofAuthor($organisation)->withLatestPublishedVersion()->create();

        $blueprint = app(BlueprintBuilder::class)
            ->withUser($identityProcess->latestPublishedVersion()->first()->full_namespace)
            ->withProcess($demoProcess->latestPublishedVersion()->first()->full_namespace)->make();

        $environment = Environment::factory()->withBlueprint($blueprint)->create();
        $fileNames = $environment->exportProcesses();

        $this->assertTrue($fileNames->isNotEmpty());

        foreach ($fileNames as $fileName) {
            $this->assertTrue(Storage::exists($fileName));
        }
    }
}
