<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Models;

use App\Http\Resources\Simulation as SimulationResource;
use App\Models\Environment;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use App\Models\Simulation;
use App\Models\Synchronization;
use App\Models\User;
use App\ProcessType\Commands\UpdateProcessTypeUnique;
use App\ProcessType\Definition;
use App\Transfer\ExportManager;
use App\Utils\ComplexityScore;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ListConfigBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class ProcessVersionTest
 * @package Tests\Unit\Models
 */
class ProcessVersionTest extends TestCase {

    use RefreshDatabase;

    public function test_process_version_has_an_id() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsString($processVersion->id);
    }

    public function test_process_version_has_a_process_id() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsString($processVersion->process_id);
    }

    public function test_process_version_has_a_calculated_process_version() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsArray($processVersion->calculated);
    }

    public function test_process_version_has_a_definition() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertInstanceOf(Definition::class, $processVersion->definition);
    }

    public function test_process_version_has_a_definition_as_array() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsArray($processVersion->definition->toArray());
    }

    public function test_process_version_has_dependencies() {
        $processVersion = ProcessVersion::factory()->withDependencies()->create();
        $this->assertIsArray($processVersion->definition->dependencies);
        $this->assertNotEmpty($processVersion->definition->dependencies);
    }

    public function test_process_version_has_a_version() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsString($processVersion->version);
    }

    public function test_process_version_has_a_changelog() {
        $processVersion = ProcessVersion::factory()->withChangelog()->create();
        $this->assertIsString($processVersion->changelog);
    }

    public function test_process_version_has_demo_data() {
        $processVersion = ProcessVersion::factory()->withDemoData()->create();
        $this->assertIsArray($processVersion->demo_data);
        $this->assertNotEmpty($processVersion->demo_data);
    }

    public function test_process_version_has_complexity_score() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertNotEmpty($processVersion->complexity_score);
    }

    public function test_process_version_has_a_namespace() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsString($processVersion->namespace);
    }

    public function test_process_version_has_a_full_namespace() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsString($processVersion->full_namespace);
    }

    public function test_process_version_has_a_latest_namespace() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsString($processVersion->latest_namespace);
    }

    public function test_process_version_has_a_develop_namespace() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsString($processVersion->develop_namespace);
    }

    public function test_process_version_has_publish_info() {
        $processVersion = ProcessVersion::factory()->create();
        $processVersion->published_at = now();
        $processVersion->save();
        $this->assertNotNull($processVersion->published_at);
    }

    public function test_process_version_has_create_info() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertNotNull($processVersion->created_at);
    }

    public function test_process_version_has_update_info() {
        $processVersion = ProcessVersion::factory()->isPublished()->create();
        $processVersion->save();
        $this->assertNotNull($processVersion->updated_at);
        $this->assertNotNull($processVersion->updated_by);
        $this->assertNotNull($processVersion->published_by);
        $this->assertInstanceOf(User::class, $processVersion->updatedUser);
        $this->assertInstanceOf(User::class, $processVersion->publisher);
    }

    public function test_process_version_has_history_head() {
        $processVersion = ProcessVersion::factory()->withHistoryHead()->create();
        $this->assertNotEmpty($processVersion->history_head);
        $this->assertInstanceOf(ProcessVersionHistory::class, $processVersion->historyHead);
    }

    public function test_process_version_has_previous_history() {
        $processVersion = ProcessVersion::factory()->withPreviousHistory()->create();
        $this->assertInstanceOf(ProcessVersionHistory::class, $processVersion->previousHistory->first());
    }

    public function test_process_version_has_succeeding_history() {
        $processVersion = ProcessVersion::factory()->withSucceedingHistory()->create();
        $this->assertInstanceOf(ProcessVersionHistory::class, $processVersion->succeedingHistory->first());
    }

    public function test_process_version_has_process() {
        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $this->assertInstanceOf(Process::class, $processVersion->process);
    }

    public function test_process_version_has_simulation() {
        $processVersion = ProcessVersion::factory()->withSimulation()->create();
        $this->assertInstanceOf(Simulation::class, $processVersion->simulations->first());
    }

    public function test_process_version_has_synchronisation() {
        /* @var ProcessVersion $processVersion */
        $processVersion = ProcessVersion::factory()->withSynchronization()->create();
        $this->assertInstanceOf(Synchronization::class, $processVersion->synchronizations->first());
        $this->assertNotEmpty($processVersion->synchronizations);
    }

    public function test_process_version_has_environment() {
        /* @var ProcessVersion $processVersion */
        $processVersion = ProcessVersion::factory()->withEnvironments()->create();
        $this->assertInstanceOf(Environment::class, $processVersion->environments->first());
        $this->assertNotEmpty($processVersion->environments);
    }

    public function test_process_version_can_check_if_it_has_previous_history() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertFalse($processVersion->hasPreviousHistory());
        $processVersion = ProcessVersion::factory()->withPreviousHistory()->create();
        $this->assertTrue($processVersion->hasPreviousHistory());
    }

    public function test_process_version_can_check_if_it_has_succeeding_history() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertFalse($processVersion->hasSucceedingHistory());
        $processVersion = ProcessVersion::factory()->withSucceedingHistory()->create();
        $this->assertTrue($processVersion->hasSucceedingHistory());
    }

    public function test_process_version_can_delete_history() {
        $processVersion = ProcessVersion::factory()->withHistoryHead()->create();
        $this->assertNotNull($processVersion->history_head);
        $processVersion->clearHistory();
        $this->assertNull($processVersion->history_head);
    }

    public function test_process_version_has_default_environment() {
        $environment = Environment::factory()->isDefault()->create();
        $processVersion = ProcessVersion::factory()->withEnvironments()->create();
        $this->assertFalse($processVersion->hasDefaultEnvironment());
        $processVersion = ProcessVersion::factory()->withEnvironments([$environment])->create();
        $this->assertTrue($processVersion->hasDefaultEnvironment());
    }

    public function test_process_version_get_default_environment() {
        $environment = Environment::factory()->isDefault()->create();
        $processVersion = ProcessVersion::factory()->withEnvironments()->create();
        $this->assertNull($processVersion->defaultEnvironment());
        $processVersion = ProcessVersion::factory()->withEnvironments([$environment])->create();
        $this->assertInstanceOf(Environment::class, $processVersion->defaultEnvironment());
    }

    public function test_process_version_has_running_simulation() {
        $simulation = Simulation::factory()->finished()->create();
        $processVersion = ProcessVersion::factory()->withSimulation([$simulation])->create();
        $this->assertFalse($processVersion->hasRunningSimulations());

        $processVersion = ProcessVersion::factory()->withSimulation()->create();
        $this->assertTrue($processVersion->hasRunningSimulations());
    }

    public function test_process_version_get_running_simulation() {
        $simulation = Simulation::factory()->create();
        $processVersion = ProcessVersion::factory()->withSimulation([$simulation])->create();
        $this->assertInstanceOf(Simulation::class, $processVersion->runningSimulations()->first());
    }

    public function test_process_version_can_return_a_running_user_simulation_of_a_logged_in_user() {
        $user = $this->login();
        $processVersion = ProcessVersion::factory()->withProcess()->withSimulationAndProcess()->create();
        $this->assertNull($processVersion->runningUserSimulation());

        $processVersion = ProcessVersion::factory()->withSimulationAndProcess(null, $user)->create();
        $this->assertInstanceOf(Simulation::class, $processVersion->runningUserSimulation());
    }

    public function test_process_version_can_return_a_running_user_simulation_of_a_logged_in_user_as_a_resource() {
        $user = $this->login();
        $processVersion = ProcessVersion::factory()->withSimulationAndProcess()->create();
        $this->assertNull($processVersion->runningUserSimulation());

        $processVersion = ProcessVersion::factory()->withSimulationAndProcess(null, $user)->create();
        $this->assertInstanceOf(SimulationResource::class, $processVersion->runningUserSimulationResource());
    }

    public function test_process_version_has_published_scope() {
        $processVersion = ProcessVersion::factory()->isPublished()->create();
        $query = $processVersion->scopePublished(ProcessVersion::query());
        $this->assertNotNull($query->where('id', '=', $processVersion->id));
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    public function test_process_version_gets_dependencies() {
        $processVersion = ProcessVersion::factory()->create();

        $this->assertNotEmpty(collect($processVersion->dependencies() ?? []));
    }

    public function test_process_version_gets_raw_definition() {
        $processVersion = ProcessVersion::factory()->create();

        $this->assertIsArray($processVersion->getRawDefintion());
        $this->assertNotEmpty($processVersion->getRawDefintion());
    }

    public function test_process_version_can_check_if_it_has_been_published() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertFalse($processVersion->isPublished());
        $processVersion = ProcessVersion::factory()->isPublished()->create();
        $this->assertTrue($processVersion->isPublished());
    }

    public function test_process_version_can_check_if_it_is_in_development() {
        $processVersion = ProcessVersion::factory()->isPublished()->create();
        $this->assertFalse($processVersion->isInDevelopment());
        $processVersion = ProcessVersion::factory()->create();
        $this->assertTrue($processVersion->isInDevelopment());
    }

    public function test_process_version_is_latest_published_version() {
        $process = Process::factory()->withLatestPublishedVersion()->create();
        $this->assertTrue($process->latestPublishedVersion->isLatestPublishedVersion());
    }

    public function test_process_version_can_be_published() {
        $this->login();
        $process = Process::factory()->ofAuthor(User::factory()->create())->create();
        $processVersion = ProcessVersion::factory()->ofProcess($process)->create();
        $processVersion->publish('1.0.0', 'published version');
        $this->assertEquals('1.0.0', $processVersion->version);
    }

    public function test_process_version_can_rollback() {
        $process = Process::factory()->ofAuthor(User::factory()->create())->create();
        $processVersion1 = ProcessVersion::factory()->ofProcess($process)->ofVersion('1.0.0')->create();
        $processVersion2 = ProcessVersion::factory()->ofProcess($process)->ofVersion('develop')->create();
        $this->assertNotEquals($processVersion2->version, $processVersion1->definition->version);
        $processVersion2->rollbackTo($processVersion1);
        $this->assertEquals('develop', $processVersion2->version);
    }

    public function test_process_version_can_create_develop_version() {
        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $newProcessVersion = $processVersion->createDevelopVersion();
        $this->assertEquals($processVersion->process->latest_version_id, $newProcessVersion->id);
    }

    public function test_process_version_can_export_definition() {
        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $this->assertEquals($processVersion->definitionExportFilePath(), $processVersion->exportDefinition());
        $this->assertIsString($processVersion->exportDefinition());
        $this->assertTrue(Storage::exists($processVersion->definitionExportFilePath()), 'Exported definition does not exist');
    }

    public function test_process_version_definition_export_has_a_filename() {
        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $this->assertIsString($processVersion->definitionExportFileName());
    }

    public function test_process_version_definition_export_has_a_filepath() {
        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $this->assertIsString($processVersion->definitionExportFilePath());
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    public function test_process_version_can_export_dependencies() {
        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $this->assertEquals($processVersion->dependenciesExportFilePath(), $processVersion->exportDependencies());
        $this->assertIsString($processVersion->exportDefinition());
        $this->assertTrue(Storage::exists($processVersion->dependenciesExportFilePath()), 'Exported dependencies does not exist');
    }

    public function test_process_version_dependencies_export_has_a_filename() {
        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $this->assertIsString($processVersion->dependenciesExportFileName());
    }

    public function test_process_version_dependencies_export_has_a_filepath() {
        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $this->assertIsString($processVersion->dependenciesExportFilePath());
    }

    public function test_process_version_get_latest_namespace_attribute() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsString($processVersion->latest_namespace);
    }

    public function test_process_version_get_develop_namespace_attribute() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsString($processVersion->develop_namespace);
    }

    public function test_process_version_get_namespace_attribute() {
        $processVersion = ProcessVersion::factory()->create();
        $this->assertIsString($processVersion->namespace);
    }

    public function test_process_version_update_complexity_score() {
        /* @var float $complexityScore */
        $processVersion = ProcessVersion::factory()->create();
        $processVersion->updateComplexityScore();
        $complexityScore = ComplexityScore::calculcate($processVersion->definition);
        $this->assertEquals($complexityScore, $processVersion->complexity_score);
    }

    public function test_process_version_user_can_get_its_available_environments() {
        $this->login();
        $process = Process::factory()->create();
        $environments = [Environment::factory()->isPublic()->create(), Environment::factory()->create()];
        $processVersion = ProcessVersion::factory()->ofProcess($process)->withEnvironments($environments)->create();

        $this->assertCount(1, $processVersion->userEnvironments());
    }

    public function test_process_version_can_find_process_version_by_namespace() {
        $process = Process::factory()->withLatestVersion()->create();
        $fullNamespace = $process->latestVersion->full_namespace;

        // Find unpublished process version ("develop").
        $this->assertInstanceOf(ProcessVersion::class, ProcessVersion::findByFullNamespace($fullNamespace));

        $process = Process::factory()->withLatestPublishedVersion()->create();
        $fullNamespace = $process->latestPublishedVersion->full_namespace;

        // Find published process version ("1.0.0").
        $this->assertInstanceOf(ProcessVersion::class, ProcessVersion::findByFullNamespace($fullNamespace, true));
    }

    // TODO Write test test_process_version_syncs_template_previous_datasets
    //    public function test_process_version_syncs_template_previous_datasets() {
    //        /* @var ProcessVersion $processVersion */
    //        $processVersion = ProcessVersion::factory()->create();
    //        $this->assertIsArray($processVersion->syncTemplatePreviewDatasets());
    //        $this->fail("has to be written");
    //    }

    // TODO Write test test_process_version_delete_previous_datasets
    //    public function test_process_version_delete_previous_datasets() {
    //        /* @var ProcessVersion $processVersion */
    //        $processVersion = ProcessVersion::factory()->create();
    //        $this->assertIsArray($processVersion->deletePreviewDatasets());
    //        $this->fail("has to be written");
    //    }

    public function test_process_version_can_export_itself_to_a_process_file() {
        $process = Process::factory()->withLatestVersion()->create();
        $this->assertIsString(ExportManager::exportToFile($process->latestVersion->full_namespace));
    }

    /**
     * @return void
     * @throws FileNotFoundException|BindingResolutionException
     */
    public function test_process_version_accesses_list_group_members_updates_dependencies() {
        $listConfig = app(ListConfigBuilder::class)->make(config('list_templates.group_members'));
        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEmpty($processVersion->dependencies()['group_aliases']);

        $listConfig->data['source']['items'] = [
            [
                'name' => 'test_alias',
                'roles' => []
            ],
            [
                'name' => 'test',
                'roles' => ['role1', 'role2']
            ]
        ];

        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();
        $processVersion->updateDependencies();

        $this->assertContains('test_alias', $processVersion->dependencies()['group_aliases']);
    }

    public function test_process_version_can_have_unique_rules() {
        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertEmpty($processVersion->definition->unique);

        $payload = [
            'type' => 'meta_data',
            'data' => ['name']
        ];

        $newDefinition = (new UpdateProcessTypeUnique($payload, $definition, $processVersion))->execute();
        $this->assertContains('name', $newDefinition->unique['meta_data']);

        $payload = [
            'type' => 'process_data',
            'data' => ['field1']
        ];

        $newDefinition = (new UpdateProcessTypeUnique($payload, $definition, $processVersion))->execute();
        $this->assertContains('field1', $newDefinition->unique['process_data']);
    }
}