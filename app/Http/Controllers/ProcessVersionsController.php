<?php

namespace App\Http\Controllers;

use App\Enums\Settings;
use App\Http\Requests\ExecuteProcessVersionSync;
use App\Http\Requests\PublishProcessVersion;
use App\Http\Requests\RestoreProcessVersion;
use App\Models\Environment;
use App\Models\ProcessVersion;
use App\Models\Setting;
use App\Models\System;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * Class ProcessVersionsController
 * @package App\Http\Controllers
 */
class ProcessVersionsController extends Controller {

    /**
     * Synchronize a process version with the added Allisa systems.
     * @param ProcessVersion $processVersion
     * @return Factory
     * @return Application|Factory|View
     */
    public function sync(ProcessVersion $processVersion) {
        if ($processVersion->process->authoredByOrganisation()) {
            $createSystemRoute = route('organisation.settings.system.create', $processVersion->process->author);
        }
        else {
            $createSystemRoute = route('settings.system.create');
        }

        return view('process-versions.sync', [
            'createSystemRoute' => $createSystemRoute,
            'processVersion' => $processVersion,
            'systems' => $processVersion->process->author->systems()->with('synchronizations')->get(),
            'title' => 'Export - ' . $processVersion->full_namespace
        ]);
    }

    /**
     * Rollback the current "develop" version to a previous version.
     * @param ProcessVersion $processVersion
     * @return Factory
     */
    public function restore(ProcessVersion $processVersion) {
        return view('process-versions.restore', [
            'process' => $processVersion->process,
            'processVersion' => $processVersion,
            'title' => 'Wiederherstellen - ' . $processVersion->full_namespace
        ]);
    }

    /**
     * Download the current version as JSON File.
     * @param ProcessVersion $processVersion
     * @return RedirectResponse|StreamedResponse
     */
    public function download(ProcessVersion $processVersion) {
        // If process is archived, download is not possible.
        if ($processVersion->process->isArchived()) {
            return back()->with('warning', 'Es können keine Prozess-Versionen von archivierten Prozessen heruntergeladen werden.');
        }
        // Only finished versions can be downloaded.
        if (!$processVersion->isPublished()) {
            return back()->with('warning', 'Die Prozessversion muss vor dem Download erst fertiggestellt werden.');
        }

        if (!$processVersion->definitionExportFileExists()) {
            $processVersion->exportDefinition();
        }

        return Storage::download($processVersion->definitionExportFilePath());
    }

    /**
     * Eine Synchronisation zu einer Allisa Plattform ausführen.
     * @param ExecuteProcessVersionSync $request
     * @param ProcessVersion $processVersion
     * @return Application|RedirectResponse|Redirector
     * @throws Exception
     */
    public function executeSync(ExecuteProcessVersionSync $request, ProcessVersion $processVersion) {
        /* @var System[] $systems */
        $systemIds = $request->validated()['system_ids'] ?? [];
        $systems = System::findMany($systemIds);

        if (!$processVersion->definitionExportFileExists()) {
            $processVersion->exportDefinition();
        }

        $synchronizations = $processVersion->syncTo($systems);
        $successes = $synchronizations->where('response_code', '=', Response::HTTP_OK);
        $failures = $synchronizations->where('response_code', '!=', Response::HTTP_OK);

        if ($successes->isNotEmpty()) {
            return redirect(route('process.versions', [
                'process' => $processVersion->process,
                'limit' => 10
            ]))->with('success', 'Erfolgreich exportiert nach: ' . $successes->pluck('system')->pluck('name')->join(', '));
        }
        if ($failures->isNotEmpty()) {
            redirect(route('process_version.sync', ['processVersion' => $processVersion]))->with('error', 'Fehler bei: ' . $failures->pluck('system')
                    ->pluck('name')
                    ->join(', '));
        }

        return redirect(route('process_version.sync', ['processVersion' => $processVersion]));
    }

    /**
     * Patch-Request. Eine Prozess-Version fertigstellen und neue "In der Entwicklung"-Version anlegen.
     * @param PublishProcessVersion $request
     * @param ProcessVersion $processVersion
     * @return RedirectResponse
     * @throws Throwable
     */
    public function publish(PublishProcessVersion $request, ProcessVersion $processVersion) {
        $validated = $request->validated();
        $version = $validated['version'];
        $changelog = $validated['changelog'] ?? null;

        // Check if version has already been published.
        if ($processVersion->published_at) {
            return back()->with('warning', 'Die Version wurde bereits veröffentlicht.');
        }

        try {
            DB::beginTransaction();

            // Publish process version and create a new "develop" version.
            $processVersion->publish($version, $changelog);
            $newProcessVersion = $processVersion->createDevelopVersion();

            // Sync the template preview datasets to the new develop version.
            $templateDatasets = Setting::retrieve(Settings::TemplatePreviewDatasets->value, [], $processVersion);
            Setting::upsertSetting(Settings::TemplatePreviewDatasets->value, $templateDatasets, $newProcessVersion);

            // Copy environments
            $copiedEnvironments = $processVersion->environments->map(fn(Environment $environment) => $environment->replicate());
            $newProcessVersion->environments()->saveMany($copiedEnvironments);

            DB::commit();

            return redirect(route('process.versions', [
                'process' => $processVersion->process,
                'limit' => 10
            ]))->with('success', 'Herzlichen Glückwunsch! Sie haben die Version "' . $version . '" fertiggestellt.');

        }
        catch (Exception $exception) {
            report($exception);

            DB::rollBack();

            return back()->with('Error', 'Die Anfrage konnte leider nicht verarbeitet werden. Bitte versuchen Sie es später erneut.');
        }
    }

    /**
     * Restore a process version by overwriting the current "develop" version with this one.
     * @param RestoreProcessVersion $request
     * @param ProcessVersion $processVersion
     * @return Application|Redirector|RedirectResponse
     */
    public function rollback(RestoreProcessVersion $request, ProcessVersion $processVersion) {
        $request->validated();
        $process = $processVersion->process;
        $developVersion = $process->latestVersion;
        $developVersion->rollbackTo($processVersion);

        return redirect(route('process.versions', $developVersion->process))->with('success', 'Die aktuelle "In der Entwicklung"-Version wurde erfolgreich zurückgesetzt.');
    }

}
