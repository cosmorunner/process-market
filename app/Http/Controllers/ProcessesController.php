<?php

namespace App\Http\Controllers;

use App\Enums\PluginSource;
use App\Enums\Visibility;
use App\Http\Requests\DeleteProcess;
use App\Http\Requests\RestoreProcess;
use App\Http\Resources\AccessibleProcesses;
use App\Http\Resources\Plugin as PluginResource;
use App\Http\Resources\ProcessWithTags;
use App\Http\Resources\UserNamespaces;
use App\Models\Organisation;
use App\Models\Plugin;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Setting;
use App\Models\Simulation;
use App\Models\User;
use App\ReferrerRedirector;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Class ProcessesController
 * @package App\Http\Controllers
 */
class ProcessesController extends Controller {

    /**
     * Redirect to home page.
     * @return RedirectResponse
     */
    public function index() {
        return redirect('/');
    }

    /**
     * Show form for creating a process.
     * @return Application|Redirector
     */
    public function create() {
        /* @var User $user */
        /* @var User|Organisation $context */
        $user = auth()->user();
        $namespace = request()->query('namespace') ?? $user->namespace;
        $context = $user->organisations->firstWhere('namespace', '=', $namespace) ?? $user;

        // If an invalid namespace was specified, redirect to user namespace.
        if ($namespace !== $user->namespace && $context instanceof User) {
            return redirect(route('process.create', ['namespace' => $user->namespace]));
        }

        $accessibleProcesses = (new AccessibleProcesses($user))->toArray(request());
        $organisationNamespaces = array_keys($accessibleProcesses['organisations'] ?? []);
        $organisations = Organisation::whereIn('namespace', $organisationNamespaces)->get();

        $organisations = $organisations->filter(fn(Organisation $organisation) => $user->roleWithin($organisation)
            ->can('processes.create'));
        $organisation = array_intersect_key($accessibleProcesses['organisations'] ?? [], array_flip($organisations->pluck('namespace')
            ->toArray()));

        return view('processes.create', [
            'namespaces' => new UserNamespaces($user),
            'selectedNamespace' => request()->query('namespace') ?? '',
            'processes' => $accessibleProcesses['processes'] ?? [],
            'licensesProcesses' => $accessibleProcesses['licensesProcesses'] ?? [],
            'organisations' => $organisation,
            'title' => 'Prozess erstellen'
        ]);
    }

    /**
     * Public detailed display of the process.
     * @param string $namespace
     * @param string $identifier
     * @return View
     */
    public function detail(string $namespace, string $identifier) {
        $process = Process::whereFullNamespace($namespace . '/' . $identifier)->firstOrFail();

        if (!$process->isPubliclyAccessible()) {
            return view('errors.403', ['exceptionTitle' => 'Prozess ist privat.']);
        }

        return view('processes.detail-information', ['process' => $process]);
    }

    /**
     * Public detailed display of the process.
     * @param string $namespace
     * @param string $identifier
     * @return View
     */
    public function detailVersions(string $namespace, string $identifier) {
        $process = Process::whereFullNamespace($namespace . '/' . $identifier)->firstOrFail();

        if (!$process->isPubliclyAccessible()) {
            return view('errors.403', ['exceptionTitle' => 'Prozess ist nicht öffentlich verfügbar.']);
        }

        return view('processes.detail-versions', ['process' => $process]);
    }

    /**
     * Purchase a process license.
     * @param Process $process
     * @return View|RedirectResponse
     */
    public function purchase(Process $process) {
        $user = auth()->user();

        if (!$process->hasPublicLicense()) {
            return back()->with('info', 'Es können keine Lizenzen von diesem Prozess erworben werden.');
        }

        $organisations = $user->organisations->filter(fn(Organisation $organisation) => $user->roleWithin($organisation)
            ->can('licenses.manage'));

        return view('processes.purchase', [
            'userNamespace' => $user->namespace,
            'organisations' => $organisations,
            'process' => $process,
            'urls' => [
                'licenses_open_source' => route('legal', 'licenses') . '#open-source',
                'privacy' => route('legal', 'privacy'),
                'store_license' => route('api.licenses.store_process_license')
            ]
        ]);
    }

    /**
     * Rule/role development of the process.
     * @param Process $process
     * @param string|null $version
     * @return Application|Factory|View|Redirector|RedirectResponse
     */
    public function develop(Process $process, string $version = null) {
        if (!$processVersion = $process->version($version)) {
            return redirect(route('index'))->with('error', 'Prozess existiert nicht.');
        }

        $simulation = $process->author->runningUserSimulations($processVersion);

        // If the logged-in user has a running simulation, the exported graph (.json file)
        // should be returned as a graph and not the current graph.
        // Background: During the simulation, the graph could already be edited further (in the case of collaboration)
        // and would therefore possibly be different from the one with which the simulation (and the Allisa system) was started.
        if ($simulation instanceof Simulation) {
            $definitionJson = json_decode(Storage::get($simulation->definitionExportFilePath()), true);
            $graphJson = json_decode(Storage::get($simulation->graphExportFilePath()), true);

            $processVersion->definition = $definitionJson['definition'];
            $processVersion->calculated = $graphJson;
        }

        // Possible running simulation.
        $simulationResource = $simulation?->asResource();

        $canUpdateVersion = auth()->user()->can('update', $processVersion) && !$process->isArchived();
        $canUpdateProcess = auth()->user()->can('update', $process) && !$process->isArchived();
        $canCompleteVersion = auth()->user()->can('complete', $processVersion);

        return view('processes.develop', [
            'process' => $process,
            'processVersion' => $processVersion,
            'version' => $version,
            'simulationResource' => $simulationResource,
            'connectorError' => $simulationResource ? ($simulationResource->additional['error'] ?? null) : null,
            'connectorErrorMessage' => $simulationResource ? ($simulationResource->additional['error_message'] ?? null) : null,
            'allisaDemoUserId' => config('allisa.simulation.user_id'),
            'allisaDemoIdentityId' => config('allisa.simulation.user_identity_id'),
            'enableUndo' => $processVersion->hasPreviousHistory(),
            'enableRedo' => $processVersion->hasSucceedingHistory(),
            'urls' => [
                'definition' => route('api.process_version.definition', $processVersion),
                'user_graphs' => route('api.user.process_versions'),
                'complete' => route('process.complete', ['process' => $process]),
                'demo' => route('process.demo', ['process' => $process, 'version' => $version ?? 'develop']),
                'config' => route('process.config', ['process' => $process, 'version' => $processVersion->version]),
                'develop' => route('process.develop', ['process' => $process, 'version' => $version]),
                'undo' => route('api.process_version.undo', $processVersion),
                'redo' => route('api.process_version.redo', $processVersion),
                'user_toggle_sidebar' => route('api.user.settings.toggle_sidebar')
            ],
            'canUpdateVersion' => $canUpdateVersion,
            'canUpdateProcess' => $canUpdateProcess,
            'canCompleteVersion' => $canCompleteVersion,
            'title' => $process->full_namespace . ' - Regeln & Daten',
            'sidebarCollapse' => Setting::retrieveUser('sidebar.collapse', false)
        ]);
    }

    /**
     * Configuration of the process.
     * @param Process $process
     * @param string|null $version
     * @return Application|Factory|View|Redirector|RedirectResponse
     */
    public function config(Process $process, string $version = null) {
        if (!$processVersion = $process->version($version)) {
            return redirect(route('index'))->with('error', 'Prozess existiert nicht.');
        }

        $user = auth()->user();
        $canUpdateVersion = $user->can('update', $processVersion);
        $canUpdateProcess = $user->can('update', $process);
        $canCompleteVersion = $user->can('complete', $processVersion);

        /* @var Collection $plugins */
        $plugins = Plugin::query()->enabled()->with('latestPublishedVersion')->get();
        $plugins = [
            PluginSource::Internal->value => [
                'actionTypeComponent' => PluginResource::collection($plugins->filter(fn(Plugin $plugin) => $plugin->isActionTypeComponentPlugin() && !$plugin->author_id)),
            ],
            PluginSource::External->value => [
                'actionTypeComponent' => PluginResource::collection($plugins->filter(fn(Plugin $plugin) => $plugin->isActionTypeComponentPlugin() && $plugin->author_id === $process->author_id)),
                'customProcessor' => PluginResource::collection($plugins->filter(fn(Plugin $plugin) => $plugin->isCustomProcessorPlugin() && $plugin->author_id === $process->author_id)),
            ],
        ];

        return view('processes.config', [
            'process' => $process,
            'processVersion' => $processVersion,
            'environments' => $processVersion->environments()->oldest()->get(),
            'simulation' => $process->author->runningUserSimulations($processVersion),
            'version' => $version,
            'title' => $process->full_namespace . ' - Konfiguration',
            'canUpdateVersion' => $canUpdateVersion,
            'canUpdateProcess' => $canUpdateProcess,
            'canCompleteVersion' => $canCompleteVersion,
            'userSettings' => [
                'config_app_full_width' => (bool) Setting::retrieveUser('config_app_full_width', false),
                'copy_element' => Setting::retrieveUser('copy_element', [])
            ],
            'urls' => [
                'definition' => route('api.process_version.definition', $processVersion),
                'user_graphs' => route('api.user.process_versions'),
                'complete' => route('process.complete', ['process' => $process]),
                'config' => route('process.config', ['process' => $process, 'version' => $processVersion->version]),
                'demo' => route('process.demo', ['process' => $process, 'version' => $processVersion->version]),
                'develop' => route('process.develop', ['process' => $process, 'version' => $processVersion->version]),
                'settings' => route('process.edit', $process),
                'undo' => route('api.process_version.undo', $processVersion),
                'redo' => route('api.process_version.redo', $processVersion),
                'copy_element' => route('api.process_version.copy_element', $processVersion),
                'delete_copy_element' => route('api.process_version.delete_copy_element', $processVersion),
                'list_support' => route('api.process_version.list_support_data', [
                    'processVersion' => $processVersion,
                    'listConfigId' => '-listConfigId-'
                ]),
                'preview_template' => route('api.process_version.templates.preview', [
                    'processVersion' => $processVersion,
                    'templateId' => '-templateId-'
                ]),
                'get_preview_datasets' => route('api.process_version.templates.preview_datasets', [
                    'processVersion' => $processVersion,
                    'templateId' => '-templateId-'
                ]),
                'update_preview_dataset' => route('api.process_version.templates.update_preview_dataset', [
                    'processVersion' => $processVersion,
                    'templateId' => '-templateId-',
                    'datasetId' => '-datasetId-'
                ]),
            ],
            'enableUndo' => $processVersion->hasPreviousHistory(),
            'enableRedo' => $processVersion->hasSucceedingHistory(),
            'plugins' => $plugins,
        ]);
    }

    /**
     * Demo simulation of a process.
     * @param Process $process
     * @param string $version
     * @return RedirectResponse|View
     */
    public function demo(Process $process, string $version) {
        if (!$processVersion = $process->version($version)) {
            return redirect(route('index'))->with('error', 'Prozess existiert nicht.');
        }

        // Due to possible redirects, set the flash messages again here.
        session()->reflash();

        $ref = request('ref') ?? request()->query('ref', '');

        if (!$simulation = $processVersion->runningUserSimulation()) {
            return redirect(route('process.start_demo', [
                    'process' => $process,
                    'version' => $processVersion->version
                ]) . '?ref=' . $ref);
        }

        // Simulations already exists, it will be forwarded to the demo
        return redirect(route('simulation.show', ['simulation' => $simulation]) . '?ref=' . $ref);
    }

    /**
     * Start a new demo simulation.
     * @param Process $process
     * @param string $version
     * @return Application|Factory|View
     */
    public function startDemo(Process $process, string $version) {
        if (!$processVersion = $process->version($version)) {
            return redirect(ReferrerRedirector::to(base64_decode(request('ref')), $process->authorProfileProcessesPath()))->with('error', 'Version existiert nicht.');
        }

        // If this URL is called up manually even though a simulation already exists.
        if ($processVersion->runningUserSimulation() instanceof Simulation) {
            return redirect(route('process.demo', [
                'process' => $processVersion->process,
                'version' => $processVersion->version
            ]));
        }

        // Forward to the demo options
        return view('processes.start-demo', [
            'processVersion' => $processVersion,
            'environments' => $processVersion->userEnvironments(),
            'title' => $process->full_namespace . ' - Demo starten',
            'organisation' => $process->author instanceof Organisation ? $process->author : null
        ]);
    }

    /**
     * Start a demo of a process from a public display.
     * @param string $namespace
     * @param string $identifier
     * @param string $version
     * @return RedirectResponse|View
     */
    public function publicDemo(string $namespace, string $identifier, string $version) {
        $process = Process::whereFullNamespace($namespace . '/' . $identifier)->firstOrFail();

        if (!$processVersion = $process->publishedVersions($version)) {
            return redirect($process->publicPath())->with('error', 'Version existiert nicht.');
        }

        // Due to possible redirects, set the flash messages again here.
        session()->reflash();

        $ref = request('ref') ?? request()->query('ref', '');

        if (!$simulation = $processVersion->runningUserSimulation()) {
            return redirect(route('process.detail.start_demo', [
                    'namespace' => $process->namespace,
                    'identifier' => $process->identifier,
                    'version' => $processVersion->version
                ]) . '?ref=' . $ref);
        }

        // Simulations already exists, it will be forwarded to the demo
        return redirect(route('simulation.show', ['simulation' => $simulation]) . '?ref=' . $ref);
    }

    /**
     * Start a demo of a process from a public display.
     * @param string $namespace
     * @param string $identifier
     * @param string $version
     * @return Application|Factory|View
     */
    public function publicStartDemo(string $namespace, string $identifier, string $version) {
        $process = Process::whereFullNamespace($namespace . '/' . $identifier)->firstOrFail();

        if ($process->hasNoLicense()) {
            return redirect(route('index'))->with('error', 'Leider kann die Anfrage aktuell nicht verarbeitet werden.');
        }

        if (!$processVersion = $process->publishedVersions($version)) {
            return redirect($process->publicPath())->with('error', 'Version existiert nicht.');
        }

        // If this URL is called up manually even though a simulation already exists.
        if ($processVersion->runningUserSimulation() instanceof Simulation) {
            return redirect(route('process.detail.demo', [
                'namespace' => $process->namespace,
                'identifier' => $process->identifier,
                'version' => $processVersion->version
            ]));
        }

        // Forward to the demo options
        return view('processes.start-demo', [
            'processVersion' => $processVersion,
            'environments' => $processVersion->userEnvironments(),
            'title' => $process->full_namespace . ' - Demo starten',
            'organisation' => null
        ]);
    }

    /**
     * Display of the form for updating the process.
     * @param Process $process
     * @return Application|Factory|View
     */
    public function edit(Process $process) {
        return view('processes.meta-data', [
            'process' => new ProcessWithTags($process),
            'hasPublishedVersion' => $process->hasPublishedVersion(),
            'title' => $process->full_namespace . ' - Daten',
            'urls' => [
                'update_process' => route('api.process.update', $process),
                'license_open_source' => route('legal', 'licenses') . '#open-source'
            ]
        ]);
    }

    /**
     * Export a process version to an Allisa system.
     * @param Process $process
     * @param string $version
     * @return Application|Factory
     */
    public function showSync(Process $process, string $version) {
        if (!$processVersion = $process->version($version)) {
            return redirect(route('index'))->with('error', 'Prozess existiert nicht.');
        }

        if ($process->authoredByOrganisation()) {
            $createSystemRoute = route('organisation.settings.system.create', $process->author);
        }
        else {
            $createSystemRoute = route('settings.system.create');
        }

        return view('process-versions.sync', [
            'createSystemRoute' => $createSystemRoute,
            'processVersion' => $processVersion,
            'systems' => $processVersion->process->author->systems()->with('synchronizations')->get(),
        ]);
    }

    /**
     * Finalize a process version.
     * @param Process $process
     * @return Application|Factory|View|RedirectResponse
     */
    public function complete(Process $process) {
        $processVersion = $process->latestVersion;
        $nextVersion = '0.0.1';

        if ($process->latestPublishedVersion) {
            $parts = explode('.', $process->latestPublishedVersion->version);
            $lastPart = (int) ($parts[2]);
            $lastPart++;
            $nextVersion = implode('.', [$parts[0], $parts[1], $lastPart]);
        }

        // Check whether version has already been published.
        if ($processVersion->published_at) {
            return back()->with('warning', 'Die Version wurde bereits veröffentlicht.');
        }

        return view('process-versions.complete', [
            'processVersion' => $processVersion,
            'process' => $processVersion->process,
            'nextVersion' => $nextVersion,
            'title' => 'Fertigstellen - ' . $processVersion->process->full_namespace
        ]);
    }

    /**
     * Display for archiving the process.
     * @param Process $process
     * @return Application|Factory|View
     */
    public function archive(Process $process) {
        return view('processes.archive', [
            'process' => $process->load('versions'),
            'title' => 'Archivieren - ' . $process->full_namespace
        ]);
    }

    /**
     * Display for restoring the archived process
     * @param Process $process
     * @return Application
     */
    public function restore(Process $process) {
        return view('processes.restore', [
            'process' => $process->load('versions'),
            'title' => 'Wiederherstellen - ' . $process->full_namespace
        ]);
    }

    /**
     * Restore the archived process
     * @param RestoreProcess $request
     * @param Process $process
     * @return Application
     */
    public function executeRestore(RestoreProcess $request, Process $process) {
        $request->validated();

        try {
            $process->update(['deleted_at' => null]);
        }
        catch (Exception $exception) {
            report($exception);
            redirect($process->authorProfileProcessesPath())->with('error', 'Fehler beim Wiederherstellen des Prozesses.');
        }

        return redirect($process->authorProfileProcessesPath())->with('success', 'Prozess wiederhergestellt.');
    }

    /**
     * All versions of a process.
     * @param Process $process
     * @return Application|Factory|View
     */
    public function versions(Process $process) {
        $limit = request()->query('limit', PHP_INT_MAX);
        $processVersions = $process->versions()->with([
            'synchronizations' => fn(MorphMany $builder) => $builder->where('license_id', '=', null),
            'publisher',
            'synchronizations.system'
        ])->limit($limit)->get([
            'id',
            'published_at',
            'published_by',
            'updated_at',
            'created_at',
            'version',
            'changelog',
            'complexity_score',
            'definition->dependencies as dependencies'
        ]);

        $process->loadCount('versions');
        $process->setRelation('versions', $processVersions);

        $processVersions->each(fn(ProcessVersion $processVersion) => $processVersion->setRelation('process', $process));

        return view('processes.versions', [
            'process' => $process,
            'systems' => $process->author->systems,
            'title' => 'Versionen - ' . $process->full_namespace
        ]);
    }

    /**
     * Delete the process.
     * @param DeleteProcess $request
     * @param Process $process
     * @return RedirectResponse
     */
    public function destroy(DeleteProcess $request, Process $process) {
        $request->validated();
        $published = $process->hasPublishedVersion();

        try {
            if ($process->visibility === Visibility::Public->value) {
                $process->updateVisibility(Visibility::Hidden->value);
            }

            // If a published version already exists, the process is only “archived”.
            $published ? $process->delete() : $process->forceDelete();
        }
        catch (Exception $exception) {
            report($exception);
            redirect($process->authorProfileProcessesPath())->with('error', $published ? 'Fehler beim Archivieren des Prozesses.' : 'Fehler beim Löschen des Prozesses.');
        }

        return redirect($process->authorProfileProcessesPath())->with('success', $published ? 'Prozess archiviert.' : 'Prozess gelöscht.');
    }

    /**
     * Display for deleting the process including all versions
     * @param Process $process
     * @return Application|Factory|View|Redirector|RedirectResponse
     */
    public function delete(Process $process) {
        if ($process->hasPublishedVersion()) {
            return redirect(route('process.archive', $process));
        }

        return view('processes.delete', [
            'process' => $process->load('versions'),
            'title' => 'Löschen - ' . $process->full_namespace
        ]);
    }

}
