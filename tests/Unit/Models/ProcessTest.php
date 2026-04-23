<?php

namespace Tests\Unit\Models;

use App\Enums\Visibility;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class ProcessTest
 * @package Tests\Unit\Models
 */
class ProcessTest extends TestCase {

    use RefreshDatabase;

    public function test_process_has_an_id() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEmpty($process->id);
    }

    public function test_process_has_a_title() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEmpty($process->title);
    }

    public function test_process_has_a_creator_id() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEmpty($process->creator_id);
    }

    public function test_process_has_a_description() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEmpty($process->description);
    }

    public function test_process_has_a_namespace() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEmpty($process->namespace);
    }

    public function test_process_has_an_identifier() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEmpty($process->identifier);
    }

    public function test_process_has_a_visibility() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertIsInt($process->visibility);
    }

    public function test_process_has_an_author_id() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEmpty($process->author_id);
    }

    public function test_process_has_an_author_type() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEmpty($process->author_type);
    }

    public function test_process_has_a_template_id() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEmpty($process->template_id);
    }

    public function test_process_has_license_options() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEmpty($process->license_options);
    }

    public function test_process_has_a_latest_version() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertIsString($process->latest_version);
    }

    public function test_process_has_a_latest_version_relation() {
        /* @var Process $process */
        $process = Process::factory()->withLatestVersion()->create();
        $this->assertNotEmpty($process->latest_version_id);

        $latestVersion = $process->versions->firstWhere(fn(ProcessVersion $processVersion) => !$processVersion->isPublished());
        $this->assertEquals($process->latest_version_id, $latestVersion->id);
        $this->assertEquals($process->latest_version_id, $process->latestVersion->id);
    }

    public function test_process_has_a_latest_published_version_relation() {
        /* @var Process $process */
        $process = Process::factory()->withLatestPublishedVersion()->create();
        $this->assertNotEmpty($process->latest_version_id);

        $latestPublishedVersion = $process->versions->firstWhere(fn(ProcessVersion $processVersion) => $processVersion->isPublished());
        $this->assertEquals($process->latest_published_version_id, $latestPublishedVersion->id);
        $this->assertEquals($process->latest_published_version_id, $process->latestPublishedVersion->id);
    }

    public function test_process_has_create_info() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEmpty($process->created_at);
    }

    public function test_process_has_a_full_namespace() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertIsString($process->full_namespace);
    }

    public function test_process_has_a_creator() {
        /* @var Process $process */
        $process = Process::factory()->ofCreator()->create();
        $this->assertInstanceOf(User::class, $process->creator);
    }

    public function test_process_has_an_author() {
        /* @var Process $process */
        $process = Process::factory()->ofAuthor()->create();
        $this->assertInstanceOf(User::class, $process->author);
    }

    public function test_process_has_a_template() {
        /* @var Process $process */
        $process = Process::factory()->withTemplate()->create();
        $this->assertInstanceOf(ProcessVersion::class, $process->template);
    }

    public function test_process_has_licenses() {
        /* @var Process $process */
        $process = Process::factory()->withLicense()->create();
        $this->assertInstanceOf(Collection::class, $process->licenses);
        $this->assertNotEmpty($process->licenses);
    }

    public function test_process_has_tags() {
        /* @var Process $process */
        $process = Process::factory()->withTags()->create();
        $this->assertInstanceOf(Collection::class, $process->tags);
        $this->assertNotEmpty($process->tags);
    }

    public function test_process_has_simulations() {
        /* @var Process $process */
        $process = Process::factory()->withSimulation()->create();
        $this->assertInstanceOf(Collection::class, $process->simulations);
        $this->assertNotEmpty($process->simulations);
    }

    public function test_process_has_a_process_version() {
        /* @var Process $process */
        $process = Process::factory()->withProcessVersion()->create();
        $this->assertInstanceOf(Collection::class, $process->versions);
        $this->assertNotEmpty($process->versions);
    }

    public function test_process_has_a_latest_process_version() {
        /* @var Process $process */
        $process = Process::factory()->withLatestVersion()->create();
        $this->assertInstanceOf(ProcessVersion::class, $process->latestVersion);
    }

    public function test_process_has_edit_path() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertIsString($process->editPath());
    }

    public function test_process_has_a_dev_path() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertIsString($process->devPath());
    }

    public function test_process_has_config_path() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertIsString($process->configPath());
    }

    public function test_process_can_return_the_owners_path() {
        /* @var Process $process */ /* @var User $user */
        /* @var Organisation $organisation */
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();

        // User
        $process = Process::factory()->ofAuthor($user)->create();
        $this->assertEquals($user->profileProcessesPath(), $process->authorProfileProcessesPath());

        // Organisation
        $process = Process::factory()->ofAuthor($organisation)->create();
        $this->assertEquals($organisation->profileProcessesPath(), $process->authorProfileProcessesPath());
    }

    public function test_process_can_return_the_owners_public_path() {
        /* @var Process $process */ /* @var User $user */
        /* @var Organisation $organisation */
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();

        // User
        $process = Process::factory()->ofAuthor($user)->create();
        $this->assertEquals($user->publicPath(), $process->authorPublicPath());

        // Organisation
        $process = Process::factory()->ofAuthor($organisation)->create();
        $this->assertEquals($organisation->publicPath(), $process->authorPublicPath());
    }

    public function test_process_has_a_demo_path() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertIsString($process->demoPath('develop'));
    }

    public function test_process_has_a_public_path() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertIsString($process->publicPath());
    }

    public function test_process_has_running_simulation() {
        /* @var Process $process */
        /* @var Simulation $simulation */
        $process = Process::factory()->withSimulation()->create();
        $simulation = $process->simulations->first();
        $simulation->save();
        $this->assertNotEmpty($process->runningSimulations()->where('id', $simulation->id)->first());
    }

    public function test_process_has_its_tags_as_a_string() {
        /* @var Process $process */
        $process = Process::factory()->withTags()->create();
        $this->assertIsString($process->tagsToString());
    }

    public function test_process_can_update_its_visibility() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertNotEquals($process->visibility, Visibility::Public->value);

        $process->updateVisibility(Visibility::Public->value);
        $process->refresh();
        $this->assertEquals($process->visibility, Visibility::Public->value);
    }

    public function test_process_can_check_whether_it_is_owned_by_an_organisation() {
        /* @var Process $process */
        $process = Process::factory()->ofAuthor(Organisation::factory()->create())->create();
        $this->assertTrue($process->authoredByOrganisation());
    }

    public function test_process_can_check_whether_it_is_owned_by_a_user() {
        /* @var Process $process */
        $process = Process::factory()->ofAuthor(User::factory()->create())->create();
        $this->assertTrue($process->authoredByUser());
    }

    public function test_process_is_publicly_accessible() {
        /* @var Process $process */
        $process = Process::factory()->create(['visibility' => Visibility::Hidden->value]);
        $this->assertTrue($process->isPubliclyAccessible());
    }

    public function test_process_is_not_archived() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertFalse($process->isArchived());
    }

    public function test_process_store_license() {
        /* @var Process $process */
        /* @var User $user */

        $process = Process::factory()->create();
        $user = $this->login();

        $validatedData = [
            "process_id" => $process->id,
            "receiver" => $user->namespace,
            "license" => [
                "level" => 'test',
                "level_options" => ['test', 'test2'],
            ]
        ];

        $this->assertIsString(Process::storeLicense($validatedData));
    }

    public function test_process_provides_private_access_to_user() {
        /* @var Process $process */
        /* @var User $user */
        $user = User::factory()->create();
        $process = Process::factory()->ofCreatorAndAuthor($user)->create();
        $this->assertTrue($process->enabledPrivateEnvironments($user));
        $process = Process::factory()->create();
        $this->assertFalse($process->enabledPrivateEnvironments($user));
    }

    public function test_process_has_published_process_version() {
        /* @var Process $process */
        /* @var ProcessVersion $processVersion */
        $process = Process::factory()->create();
        ProcessVersion::factory()->ofProcess($process)->isPublished()->create();

        $this->assertInstanceOf(Collection::class, $process->publishedVersions());
        $this->assertNotEmpty($process->publishedVersions());
    }

    public function test_process_can_identify_its_latest_process_version() {
        /* @var Process $process */
        $process = Process::factory()->withLatestVersion()->create();
        $this->assertInstanceOf(ProcessVersion::class, $process->latestVersion);
    }

    public function test_process_can_return_a_specific_version() {
        // Process with unpublished "develop" version
        $process = Process::factory()->withLatestVersion()->create();
        $version = $process->latestVersion->version;
        $this->assertNull($process->version($version, true));
        $this->assertInstanceOf(ProcessVersion::class, $process->version($version));

        // Process with published "1.0.0" version
        $process = Process::factory()->withLatestPublishedVersion()->create();
        $version = $process->latestPublishedVersion->version;
        // By default, version() method checks any version
        $this->assertNull($process->version($version, false));
        $this->assertInstanceOf(ProcessVersion::class, $process->version($version, true));

        // Return "latest" published version.
        $process = Process::factory()->withLatestPublishedVersion()->create();

        $this->assertNull($process->version($version, false));
        $this->assertInstanceOf(ProcessVersion::class, $process->version('latest', true));
    }

    public function test_process_can_find_a_process_with_a_namespace() {
        /* @var Process $process */
        $process = Process::factory()->create();
        $this->assertInstanceOf(Process::class, Process::whereFullNamespace($process->full_namespace)->first());
    }

    public function test_process_can_identify_whether_it_has_a_published_version() {
        /* @var ProcessVersion $processVersion */
        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $processVersion->process->latest_published_version_id = null;
        $processVersion->save();
        $this->assertFalse($processVersion->process->hasPublishedVersion());

        $processVersion = ProcessVersion::factory()->withProcess()->create();
        $this->assertTrue($processVersion->process->hasPublishedVersion());
    }

    public function test_process_can_check_whether_it_is_public() {
        $process = Process::factory()->withVisibility(Visibility::Public->value)->create();
        $this->assertTrue($process->isPubliclyAccessible());
    }

    public function test_process_can_create_initial_version() {
        $process = Process::factory()->create();
        $processVersion = $process->createInitialVersion();

        $this->assertInstanceOf(ProcessVersion::class, $processVersion);
        $this->assertEquals('develop', $processVersion->version);
        $this->assertEquals('develop', $processVersion->definition->version);
    }

}