<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\ProcessVersionsController;
use App\Models\Process;
use App\Models\ProcessVersion;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ListConfigBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * For each controller route, the following is tested:
 * - Route and controller action exists
 * - Controller action is correctly authenticated
 * - Controller action is correctly authorized
 * - Controller action validation exception is thrown
 * - Controller action possible return value(s) is/are correct
 * - Controller action intended logic is correct
 * Class ProcessVersionsControllerTest
 * @package Tests\Feature\Api
 */
class ProcessVersionsControllerTest extends TestCase {

    use RefreshDatabase;

    public function test_api_process_versions_controller_definition_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.definition'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'definition'));
    }

    public function test_api_process_versions_definition_unauthenticated_user_returns_unauthorized() {
        $processVersion = ProcessVersion::factory()->create();

        $this->get(route('api.process_version.definition', [
            'processVersion' => $processVersion
        ]))->assertUnauthorized();
    }

    public function test_api_process_versions_definition_unauthorized_user_returns_forbidden() {
        $this->login();
        $processVersion = ProcessVersion::factory()->withProcess()->create();

        $this->get(route('api.process_version.definition', [
            'processVersion' => $processVersion
        ]))->assertForbidden();
    }

    public function test_api_process_versions_definition_authorized_user_returns_valid_data() {
        $user = $this->login();
        $processVersion = ProcessVersion::factory()->withHistoryHead()->create();
        Process::factory()->ofAuthor($user)->withProcessVersion([$processVersion])->create();

        $json = $this->get(route('api.process_version.definition', [
            'processVersion' => $processVersion
        ]))->assertOk()->assertJsonIsObject()->json();

        // TODO: Check return value structure
        $this->assertEquals($json, $processVersion->definition->toArray());
    }

    public function test_api_process_versions_preview_datasets_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.templates.preview_datasets'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'previewDatasets'));
    }

    public function test_api_process_versions_preview_datasets_unauthenticated_user_returns_unauthorized() {
        $processVersion = ProcessVersion::factory()->create();

        $this->get(route('api.process_version.definition', [
            'processVersion' => $processVersion,
            'template' => Str::uuid()->toString()
        ]))->assertUnauthorized();
    }

    public function test_api_process_versions_update_definition_authorized_user_can_update_own_process_version_definition() {
        $user = $this->login();
        $processVersion = ProcessVersion::factory()->withHistoryHead()->create();
        Process::factory()->ofAuthor($user)->withProcessVersion([$processVersion])->create();

        $actionType = app(ActionTypeBuilder::class)->make(['reference' => 'demo']);
        $response = $this->patch(route('api.process_version.update_definition', $processVersion), [
            'command' => 'StoreActionType',
            'payload' => $actionType->toArray()
        ]);
        $response->assertOK();
    }

    public function test_api_process_versions_list_support_data_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.list_support_data'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'listSupportData'));
    }

    public function test_api_process_versions_list_support_data_unauthenticated_user_returns_unauthorized() {
        $listConfig = app(ListConfigBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->get(route('api.process_version.list_support_data', [
            'processVersion' => $processVersion,
            'listConfigId' => $listConfig->id
        ]))->assertUnauthorized();
    }

    public function test_api_process_versions_list_support_data_unauthorized_user_returns_forbidden() {
        $this->login();
        $listConfig = app(ListConfigBuilder::class)->make();
        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        // User needs at least the permission to view the process of the version.
        $this->get(route('api.process_version.list_support_data', [
            'processVersion' => $processVersion,
            'listConfigId' => $listConfig->id
        ]))->assertForbidden();
    }

    public function test_api_process_versions_list_support_data_missing_list_config_returns_empty_array() {
        $user = $this->login();

        $definition = app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();
        $process = Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withLatestPublishedVersion($processVersion)
            ->withLatestVersion($processVersion)
            ->create();

        // Check if request returns empty json
        $this->get(route('api.process_version.list_support_data', [
            'processVersion' => $process->latestVersion,
            // Here we pass a random list config id.
            'listConfigId' => Str::uuid()->toString()
        ]))->assertJson([]);
    }

    public function test_api_process_versions_list_support_data_template_processes_returns_valid_data() {
        $user = $this->login();

        $listConfig = app(ListConfigBuilder::class)->withTemplate('processes')->make();
        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();
        $process = Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withLatestPublishedVersion($processVersion)
            ->withLatestVersion($processVersion)
            ->create();

        // Check if request is ok.
        $json = $this->get(route('api.process_version.list_support_data', [
            'processVersion' => $process->latestVersion,
            'listConfigId' => $listConfig->id
        ]))->assertOk()->assertJsonIsObject()->json();

        // Check if all necessary keys exist.
        $this->assertCount(11, $json);
        $this->assertEmpty(array_diff([
            'coreTableColumns',
            'processes',
            'statusTypes',
            'outputs',
            'allColumns',
            'systemUrls',
            'processUrls',
            'definitions',
            'select'
        ], array_keys($json)));

    }

    public function test_api_process_versions_list_support_data_template_group_members_returns_valid_data() {
        $user = $this->login();
        $listConfig = app(ListConfigBuilder::class)->withTemplate('group_members')->make();

        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();
        $process = Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withLatestPublishedVersion($processVersion)
            ->withLatestVersion($processVersion)
            ->create();

        // Check if request is ok.
        $json = $this->get(route('api.process_version.list_support_data', [
            'processVersion' => $process->latestVersion,
            'listConfigId' => $listConfig->id
        ]))->assertOk()->assertJsonIsObject()->json();

        // Check if all necessary keys exist.
        $this->assertCount(2, $json);
        $this->assertArrayHasKey('select', $json);
        $this->assertArrayHasKey('allColumns', $json);
    }

    public function test_api_process_versions_list_support_data_template_relation_returns_valid_data() {
        $user = $this->login();

        $listConfig = app(ListConfigBuilder::class)->withTemplate('relation')->make();
        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();
        $process = Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withLatestPublishedVersion($processVersion)
            ->withLatestVersion($processVersion)
            ->create();

        // Check if request is ok.
        $json = $this->get(route('api.process_version.list_support_data', [
            'processVersion' => $process->latestVersion,
            'listConfigId' => $listConfig->id
        ]))->assertOk()->assertJsonIsObject()->json();

        // Check that return value is correct.
        $this->assertCount(2, $json);
        $this->assertArrayHasKey('select', $json);
        $this->assertArrayHasKey('allColumns', $json);
    }

    public function test_api_process_versions_list_support_data_support_template_connector_request_returns_valid_data() {
        $user = $this->login();

        $listConfig = app(ListConfigBuilder::class)->withTemplate('connector_request')->make();
        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();
        $process = Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withLatestPublishedVersion($processVersion)
            ->withLatestVersion($processVersion)
            ->create();

        // Check if request is ok.
        $json = $this->get(route('api.process_version.list_support_data', [
            'processVersion' => $process->latestVersion,
            'listConfigId' => $listConfig->id
        ]))->assertOk()->assertJsonIsObject()->json();

        // Check that return value is correct.
        $this->assertCount(1, $json);
        $this->assertArrayHasKey('select', $json);
    }

    public function test_api_process_versions_list_support_data_support_only_returns_parts() {
        $user = $this->login();

        $listConfig = app(ListConfigBuilder::class)->withTemplate('processes')->make();
        $definition = app(DefinitionBuilder::class)->withListConfigs([$listConfig])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();
        $process = Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withLatestPublishedVersion($processVersion)
            ->withLatestVersion($processVersion)
            ->create();

        $json = $this->get(route('api.process_version.list_support_data', [
            'processVersion' => $process->latestVersion,
            'listConfigId' => $listConfig->id,
            'parts' => 'allColumns,definitions'
        ]))->json();

        $this->assertCount(2, $json);
        $this->assertArrayHasKey('allColumns', $json);
        $this->assertArrayHasKey('definitions', $json);
    }

    public function test_api_process_versions_controller_update_proview_datasets_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.templates.update_preview_dataset'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'updatePreviewDataset'));
    }

    public function test_api_process_versions_controller_preview_template_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.templates.preview'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'previewTemplate'));
    }

    public function test_api_process_versions_controller_update_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.update'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'update'));
    }

    public function test_api_process_versions_controller_update_demo_data_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.update_demo_data'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'updateDemoData'));
    }

    public function test_api_process_versions_controller_store_environment_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.store_environment'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'storeEnvironment'));
    }

    public function test_api_process_versions_controller_syntax_values_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.syntax_values'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'syntaxValues'));
    }

    public function test_api_process_versions_controller_update_definition_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.update_definition'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'updateDefinition'));
    }

    public function test_api_process_versions_controller_update_environment_blueprint_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.update_environment_blueprint'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'updateEnvironmentBlueprint'));
    }

    public function test_api_process_versions_controller_update_environment_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.update_environment'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'updateEnvironment'));
    }

    public function test_api_process_versions_controller_copy_environment_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.copy_environment'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'copyEnvironment'));
    }

    public function test_api_process_versions_controller_delete_environment_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.delete_environment'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'deleteEnvironment'));
    }

    public function test_api_process_versions_controller_undo_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.undo'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'undo'));
    }

    public function test_api_process_versions_controller_redo_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.redo'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'redo'));
    }

    public function test_api_process_versions_controller_copy_element_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.copy_element'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'copyElement'));
    }

    public function test_api_process_versions_controller_delete_copy_element_route_and_action_exists() {
        $this->assertTrue(Route::has('api.process_version.delete_copy_element'));
        $this->assertTrue(method_exists(ProcessVersionsController::class, 'deleteCopyElement'));
    }

}