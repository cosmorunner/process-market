<?php

namespace Tests;

use App\Environment\Blueprint;
use App\Models\Environment;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\User;
use App\ProcessType\Definition;
use Database\Builder\Definition\DefinitionBuilder;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Passport;
use Throwable;

/**
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends BaseTestCase {

    use CreatesApplication;

    /**
     * Set up method run before every test.
     * @return void
     * @throws Exception
     */
    public function setUp(): void {
        parent::setUp();

        // As a safety measure, we make sure that unit tests always run with the "testing" environment.
        if (App::environment() !== 'testing' || App::environmentFile() !== '.env.testing') {
            throw new Exception('Error: Tests must run with the "testing" (.env.testing) environment, not: ' . App::environment() . ', file: ' . App::environmentFile());
        }

        // To run tests in parallel, we create a new folder for each running test process.
        Config::set('filesystems.disks.testing.root', config('filesystems.disks.testing.root') . ParallelTesting::token());

        // Storage root directory must be called "testing". Safety measure to not accidentally overwrite data.
        if (!Str::endsWith(Storage::getConfig()['root'], 'testing' . ParallelTesting::token())) {
            throw new Exception('Error: Testing storage root directory must be called "testing". See "testing" filesystem disc.');
        }

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');

        // Always run without redis. Overwrite when necessary in test.
        Config::set('app.redis_enabled', false);
    }

    /**
     * Tear down method to run after each test.
     * @throws Throwable
     */
    public function tearDown(): void {
        $this->cleanUpGeneratedFiles();

        parent::tearDown();
    }

    /**
     * Login for a user.
     * @param User|null $user
     * @return User
     */
    protected function login($user = null): User {
        /* @var User $user */
        $user = $user ?: User::factory()->create();
        $this->actingAs($user);

        // Damit API Aufrufe (/routes/api.php) funktionieren.
        Passport::actingAs($user);

        return $user;
    }

    /**
     * Returns a Process with version history and user as creator and author
     * @param Definition|null $definition
     * @return Process
     * @throws BindingResolutionException
     */
    protected function createProcessWithVersionHistoryAndUser(Definition $definition = null): Process {
        $user = $this->login();
        $definition = $definition ?? app(DefinitionBuilder::class)->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        return Process::factory()
            ->ofCreatorAndAuthor($user)
            ->withProcessVersion([$processVersion])
            ->create(['latest_version_id' => $processVersion->id]);
    }

    /**
     * Removes created documents and templates.
     */
    public function cleanUpGeneratedFiles() {
        $files = collect(Storage::files(null, true))->filter(function ($item) {
            return !Str::endsWith($item, 'gitignore');
        });

        Storage::delete($files->toArray());
    }

    /**
     * Call the api route to update the definition of a process version with a command and a corresponding payload.
     * @param ProcessVersion $processVersion
     * @param string $command
     * @param array $payload
     * @return TestResponse
     */
    public function updateDefinition(ProcessVersion $processVersion, string $command, array $payload) {
        return $this->patch(route('api.process_version.update_definition', $processVersion), [
            'command' => $command,
            'payload' => $payload
        ]);
    }

    /**
     * Call the api route to update the blueprint a process version environment with a command and a corresponding payload.
     * @param ProcessVersion $processVersion
     * @param Environment $environment
     * @param string $command
     * @param array $payload
     * @return TestResponse
     */
    public function updateEnvironmentBlueprint(ProcessVersion $processVersion, Environment $environment, string $command, array $payload) {
        return $this->patch(route('api.process_version.update_environment_blueprint', [
            'processVersion' => $processVersion,
            'environment' => $environment
        ]), [
            'command' => $command,
            'payload' => $payload
        ]);
    }

    /**
     * Loggs in a user and returns a fully set up empty unpublished (develop) process version with a default environment.
     * @param Definition|null $definition
     * @param Blueprint|null $blueprint Environment-Blueprint
     * @return ProcessVersion
     */
    public function fullySetupProcessVersionWithEnvironment(Definition $definition = null, Blueprint $blueprint = null): ProcessVersion {
        $processVersion = ProcessVersion::factory()->make();

        try {
            $user = $this->login();
            $process = Process::factory()->ofCreatorAndAuthor($user)->withoutLatestPublishedVersion()->create();
            $definition = $definition ?? app(DefinitionBuilder::class)->make();
            $environment = Environment::factory()->emptyWithName('Default', $blueprint)->create();

            $processVersion = ProcessVersion::factory()
                ->withDefinition($definition)
                ->withEnvironments([$environment])
                ->ofProcess($process)
                ->create();
        }
        catch (BindingResolutionException) {
            // Ignore
        }

        return $processVersion;
    }

    /**
     * Loggs in a user and returns a fully set up process version environment belonging to an unpublished (develop) process version.
     * @param Blueprint|null $blueprint
     * @return Environment
     */
    public function fullySetupEnvironment(Blueprint $blueprint = null) {
        $processVersion = $this->fullySetupProcessVersionWithEnvironment(blueprint: $blueprint);

        return $processVersion->environments->first();
    }

}
