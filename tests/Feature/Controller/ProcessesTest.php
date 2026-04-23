<?php

namespace Tests\Feature\Controller;

use App\Enums\Visibility;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\User;
use App\Transfer\ImportManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class ProcessesTest
 * @package Tests\Feature\Routes
 */
class ProcessesTest extends TestCase {

    use WithFaker, RefreshDatabase;

    public function test_processes_processes_unauthenticated_user_cannot_create_a_process() {
        $this->get(route('process.create'))->assertRedirect(route('login'));
    }

    public function test_processes_authenticated_user_can_create_a_process() {
        /* @var Process $process */
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->make();
        $this->get(route('process.create'))->assertOk();
        $response = $this->post(route('api.process.store'), $process->toArray())->assertCreated();
        $response->assertJson(['redirect' => route('process.develop', Process::first())]);
    }

    public function test_processes_process_is_created_with_develop_version() {
        $user = $this->login();
        $data = [
            'title' => 'Test',
            'namespace' => $user->namespace,
            'identifier' => 'test'
        ];

        $this->post(route('api.process.store'), $data)->assertCreated();
        $process = $user->processes()->whereIdentifier('test')->firstOrFail();

        $this->assertInstanceOf(ProcessVersion::class, $process->latestVersion);
        $this->assertEquals($process->latest_version, $process->latestVersion->version);
        $this->assertEquals('develop', $process->latest_version);
        $this->assertEquals($process->namespace . '/' . $process->identifier . '@develop', $process->latestVersion->full_namespace);
        $this->assertNull($process->latestPublishedVersion);
        $this->assertNull($process->latest_published_version_id);
    }

    public function test_processes_process_created_is_not_published() {
        $user = $this->login();
        $data = [
            'title' => 'Test',
            'namespace' => $user->namespace,
            'identifier' => 'test'
        ];

        $this->post(route('api.process.store'), $data)->assertCreated();
        $process = $user->processes()->whereIdentifier('test')->firstOrFail();
        $this->assertCount(1, $process->versions);
        $this->assertFalse($process->latestVersion->isPublished());
        $this->assertEmpty($process->publishedVersions());
    }

    public function test_processes_process_is_created_with_private_visibility() {
        $user = $this->login();
        $data = [
            'title' => 'Test',
            'namespace' => $user->namespace,
            'identifier' => 'test'
        ];

        $this->post(route('api.process.store'), $data)->assertCreated();
        $process = $user->processes()->whereIdentifier('test')->firstOrFail();

        $this->assertEquals(Visibility::Private->value, $process->visibility);

        // Private process can not be opened
        $this->get(route('process.detail', ['namespace' => $process->namespace, 'identifier' => $process->identifier]))
            ->assertViewIs('errors.403');
        $this->assertFalse($process->isPubliclyAccessible());
    }

    public function test_processes_process_is_created_with_default_empty_template() {
        $user = $this->login();
        $data = [
            'title' => 'Test',
            'namespace' => $user->namespace,
            'identifier' => 'test'
        ];

        $this->post(route('api.process.store'), $data)->assertCreated();
        $process = $user->processes()->whereIdentifier('test')->firstOrFail();

        // We unset input data and "list_configs" as the array representation differs from the "empty" template.
        $empty = config('graph.empty');
        unset($empty['list_configs'], $empty['name'], $empty['namespace'], $empty['identifier']);

        $definition = $process->latestVersion->definition->toArray();
        unset($definition['list_configs'], $definition['name'], $definition['namespace'], $definition['identifier']);

        $process = $user->processes()->whereIdentifier('test')->firstOrFail();
        $this->assertTrue(Storage::exists($process->latestVersion->definitionExportFilePath()), 'Definition export does not exist.');
        $this->assertTrue(Storage::exists($process->latestVersion->dependenciesExportFilePath()), 'Dependencies export does not exist.');
        $this->assertEquals($empty, $definition);
    }

    public function test_processes_process_is_created_with_a_process_template() {
        $user = $this->login(User::factory()->create(['namespace' => 'bob']));
        $process = Process::factory()->withLatestPublishedVersion()->ofCreatorAndAuthor($user)->create([
            'namespace' => 'template',
            'identifier' => 'process'
        ]);

        $data = [
            'title' => 'Test',
            'namespace' => 'bob',
            'identifier' => 'test',
            'template' => $process->full_namespace
        ];

        $this->post(route('api.process.store'), $data)->assertCreated();
        $newProcess = $user->processes()->whereIdentifier('test')->firstOrFail();
        $this->assertTrue(Storage::exists($newProcess->latestVersion->definitionExportFilePath()), 'Definition export does not exist.');
        $this->assertTrue(Storage::exists($newProcess->latestVersion->dependenciesExportFilePath()), 'Dependencies export does not exist.');

        // When creating a process based on a template, the template namespace is discarded, so it should not be visible anywhere anymore
        // in the new process.
        $this->assertEquals('bob/test', $newProcess->full_namespace);
        $this->assertEquals('bob/test@develop', $newProcess->latestVersion->full_namespace);
        $this->assertEquals('bob/test@develop', $newProcess->latestVersion->definition->fullNamespaceWithVersion());
        $this->assertEquals('bob/test@develop', ProcessVersion::whereFullNamespace('bob/test@develop')->first()->full_namespace);
    }

    public function test_processes_version_is_published_correctly() {
        $user = $this->login();
        $data = [
            'title' => 'Test',
            'namespace' => $user->namespace,
            'identifier' => 'test'
        ];

        $this->post(route('api.process.store'), $data)->assertCreated();
        $process = $user->processes()->whereIdentifier('test')->firstOrFail();

        // Current version is not published
        $this->assertFalse($process->latestVersion->isPublished());

        // Check that "Fertigstellen" route can be opened.
        $this->get(route('process.complete', $process))->assertOk()->assertSee('Fertigstellen');

        // Invalid version, must be at least "0.0.1"
        $this->patch(route('process_version.publish', $process->latestVersion), ['version' => '0.0.0'])
            ->assertSessionHasErrors('version');

        // Valid version, check if redirect to versions page is correct.
        $this->followingRedirects()
            ->patch(route('process_version.publish', $process->latestVersion), ['version' => '0.0.1'])
            ->assertViewIs('processes.versions')
            ->assertSee('0.0.1');

        // Reload model attributes
        $process->refresh();
        $this->assertEquals($process->latest_published_version_id, $process->latestPublishedVersion->id);
        $this->assertCount(1, $process->publishedVersions());

        // When publishing a version, the "latest" and concrete version is exported
        $this->assertTrue($process->latestPublishedVersion->definitionExportFileExists('latest'));
        $this->assertTrue($process->latestPublishedVersion->dependenciesExportFileExists());
        $this->assertTrue($process->latestPublishedVersion->definitionExportFileExists('latest'));
        $this->assertTrue($process->latestPublishedVersion->dependenciesExportFileExists());

        // Check if exported files have correct version by importing exported file.
        $definition = ImportManager::readDefinition($process->latestPublishedVersion->full_namespace);
        $this->assertEquals($process->latestPublishedVersion->version, $definition->version);
    }

    public function test_processes_unpublished_process_is_hard_deleted_when_deleted() {
        /* @var Process $process */
        $process = Process::factory()
            ->withoutLatestPublishedVersion()
            ->withLatestVersion()
            ->ofCreatorAndAuthor($this->login())
            ->create();

        // No published versions yet
        $this->assertEmpty($process->publishedVersions());

        // Without published versions, the process should be completely deleted from the database.
        $this->delete(route('process.destroy', ['process' => $process]), ['accept' => 'on'])
            ->assertRedirect(route('profile.processes'));

        $this->assertDatabaseMissing('processes', $process->only('id'));
    }

    public function test_processes_published_process_is_soft_deleted_when_deleted() {
        /* @var Process $process */
        $process = Process::factory()->withLatestPublishedVersion()->ofCreatorAndAuthor($this->login())->create();
        $this->assertNotEmpty($process->publishedVersions());

        // With published versions, the process should be soft deleted (set "deleted_at" attribute).
        $this->delete(route('process.destroy', ['process' => $process]), ['accept' => 'on'])
            ->assertRedirect(route('profile.processes'));

        $process = Process::withTrashed()->first();
        $this->assertDatabaseHas('processes', $process->only('id'));
        $this->assertNotEmpty($process->deleted_at);
    }

    public function test_processes_an_unauthenticated_user_cannot_delete_processes() {
        $process = Process::factory()->create();
        $this->delete(route('process.destroy', ['process' => $process]), ['accept' => 'on'])->assertRedirect(route('login'));
    }

    public function test_processes_a_user_can_update_his_process() {
        /* @var Process $process */
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();

        $this->patch(route('api.process.update', $process), [
            'title' => 'Changed',
            'description' => 'Changed',
            'visibility' => Visibility::Private->value,
            'tags' => ['tag1']
        ])->assertOk();

        $process->refresh();
        $this->assertEquals('Changed', $process->title);
        $this->assertEquals('Changed', $process->description);
        $this->assertEquals(Visibility::Private->value, $process->visibility);
        $this->assertEquals('tag1', $process->tagsToString());
    }

    public function test_processes_a_user_can_update_license() {
        /* @var Process $process */
        $process = Process::factory()->withVisibility(Visibility::Private->value)->ofCreatorAndAuthor($this->login())->create();

        $this->assertEquals(Visibility::Private->value, $process->visibility);

        // Check that license terms must be accepted.
        $this->patch(route('api.process.update', $process), [
            'title' => $process->title,
            'visibility' => $process->visibility,
            'license_options' => [
                [
                    'level' => 'open-source',
                    'level_options' => []
                ]
            ],
        ])->assertInvalid('accept_license');

        // Open source licence cannot be used with private visibility
        $this->patch(route('api.process.update', $process), [
            'title' => $process->title,
            'visibility' => Visibility::Private->value,
            'description' => $process->description,
            'accept_license' => 1,
            'license_options' => [
                [
                    'level' => 'open-source',
                    'level_options' => []
                ]
            ],
        ])->assertOk();

        // Valid open source license request
        $this->patch(route('api.process.update', $process), [
            'title' => $process->title,
            'visibility' => $process->visibility,
            'description' => $process->description,
            'accept_license' => 1,
            'license_options' => [
                [
                    'level' => 'open-source',
                    'level_options' => []
                ]
            ],
        ])->assertOk();

        $process->refresh();
    }

    public function test_processes_an_authenticated_user_can_view_a_public_process() {
        /* @var Process $process */
        $user = $this->login();
        $process = Process::factory()->ofAuthor($user)->withProcessVersion()->withVisibility(Visibility::Public->value)->create();
        $this->get(route('process.detail', ['namespace' => $process->namespace, 'identifier' => $process->identifier]))
            ->assertOk();
    }

    public function test_processes_an_unauthenticated_user_cannot_view_a_private_process() {
        $process = Process::factory()->withProcessVersion()->withVisibility(Visibility::Private->value)->create();

        $this->get(route('process.detail', ['namespace' => $process->namespace, 'identifier' => $process->identifier]))
            ->assertOk()
            ->assertViewIs('errors.403');
    }

    public function test_processes_a_user_can_view_his_private_process() {
        /* @var Process $process */
        $process = Process::factory()
            ->ofCreatorAndAuthor($this->login())
            ->withProcessVersion()
            ->withVisibility(Visibility::Private->value)
            ->create();

        $this->get(route('process.detail', [
            'namespace' => $process->namespace,
            'identifier' => $process->identifier
        ]))->assertOk();
    }

    public function test_processes_a_processes_can_have_running_simulations() {
        /* @var Process $process */
        $process = Process::factory()->withProcessVersion()->withSimulation()->create();
        $this->assertCount(2, $process->runningSimulations());
    }

    public function test_processes_authorized_user_can_view_graph_of_completed_version() {
        $user = $this->login();
        $process = Process::factory()->ofAuthor($user)->withLatestVersion()->withLatestPublishedVersion()->create();
        $this->assertInstanceOf(ProcessVersion::class, $process->latestPublishedVersion);
        $version = $process->latestPublishedVersion->version;

        $this->get(route('process.develop', ['process' => $process, 'version' => $version]))->assertOk();
    }

    public function test_processes_authorized_user_can_view_config_of_completed_version() {
        $user = $this->login();
        $process = Process::factory()->ofAuthor($user)->withLatestVersion()->withLatestPublishedVersion()->create();
        $this->assertInstanceOf(ProcessVersion::class, $process->latestPublishedVersion);
        $version = $process->latestPublishedVersion->version;

        $this->get(route('process.config', ['process' => $process, 'version' => $version]))->assertOk();

    }

}
