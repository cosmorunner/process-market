<?php

namespace App\Http\Controllers;

use App\Enums\Visibility;
use App\Http\Requests\DeleteSolution;
use App\Http\Resources\SolutionWithTags;
use App\Http\Resources\UserNamespaces;
use App\Models\Demo;
use App\Models\Organisation;
use App\Models\Solution;
use App\Models\User;
use App\ReferrerRedirector;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class SolutionsController
 * @package App\Http\Controllers
 */
class SolutionsController extends Controller {

    /**
     * Redirect zur Startseite.
     * @return RedirectResponse
     */
    public function index() {
        return redirect('/');
    }

    /**
     * Formular zum Anlegen eines Prozesses zeigen.
     * @return View
     */
    public function create() {
        /* @var User $user */
        $user = auth()->user();

        return view('solutions.create', [
            'namespaces' => new UserNamespaces($user),
            'selectedNamespace' => request()->query('namespace') ?? '',
            'title' => 'Lösung erstellen'
        ]);
    }

    /**
     * Öffentliche Detail-Anzeige der Lösung.
     * @param string $namespace
     * @param string $identifier
     * @return View
     */
    public function detail(string $namespace, string $identifier) {
        $solution = Solution::whereFullNamespace($namespace . '/' . $identifier)->firstOrFail();
        $solution->loadProcessVersionsOfVersions();

        if (!$solution->isPubliclyAccessible()) {
            return view('errors.403', [
                'exceptionTitle' => 'Lösung ist privat.'
            ]);
        }

        return view('solutions.detail-information', [
            'solution' => $solution,
            'solutionVersion' => $solution->publicVersions('latest'),
            'title' => $solution->full_namespace
        ]);
    }

    /**
     * Alle öffentlichen Versionen einer Solution.
     * @param string $namespace
     * @param string $identifier
     * @return Application|Factory|View
     */
    public function detailVersions(string $namespace, string $identifier) {
        $solution = Solution::whereFullNamespace($namespace . '/' . $identifier)->firstOrFail();
        $solution->loadProcessVersionsOfVersions();

        if (!$solution->isPubliclyAccessible()) {
            return view('errors.403', ['exceptionTitle' => 'Lösung ist privat.']);
        }

        return view('solutions.detail-versions', [
            'solution' => $solution,
            'solutionVersion' => $solution->publicVersions('latest'),
            'versions' => $solution->publicVersions(),
            'title' => 'Versionen - ' . $solution->full_namespace
        ]);
    }

    /**
     * Kaufen einer Lösung-Lizenz.
     * @param Solution $solution
     * @param string $version Optionale Angabe einer Solution Version, von der eine Lizenz erworben werden soll.
     * @return View|RedirectResponse
     */
    public function purchase(Solution $solution, string $version) {
        /* @var User $user */
        $user = auth()->user();

        if (!$solution->hasPublicLicense()) {
            return back()->with('info', 'Es können keine Lizenzen von dieser Lösung erworben werden.');
        }

        $unavailableSolutionVersion = false;
        $latestSolutionVersion = $solution->publicVersions($version);

        if (!$latestSolutionVersion) {
            return back()->with('info', 'Diese Lösung steht aktuell nicht zur Verfügung.');
        }

        if ($version === 'latest') {
            $version = $solution->latestPublishedVersion->version;
        }

        if ($latestSolutionVersion->version !== $version) {
            $unavailableSolutionVersion = true;
            $latestSolutionVersion = $solution->publicVersions();
        }

        $organisations = $user->organisations->filter(fn(Organisation $organisation) => $user->roleWithin($organisation)->can('licenses.manage'));

        // Falls die angefragte Version nicht zur Verfügung steht, die tatsächlich aktuellste Version laden.
        return view('solutions.purchase', [
            'userNamespace' => $user->namespace,
            'organisations' => $organisations,
            'solution' => $solution,
            'latestPublicSolutionVersion' => $latestSolutionVersion,
            'unavailableVersion' => $unavailableSolutionVersion,
            'queryNamespace' => request()->query('namespace'),
            'urls' => [
                'licenses' => route('legal', 'licenses'),
                'licenses_mixed' => route('legal', 'licenses') . '#mixed',
                'privacy' => route('legal', 'privacy'),
                'store_license' => route('api.licenses.store_solution_license'),
                'get_process_licenses' => route('api.licenses.solution', ['ownerNamespace' => '#ownerNamespace#', 'solution' => $solution->id])
            ]
        ]);
    }

    /**
     * Demo einer Läsung
     * @param Solution $solution
     * @param string $version
     * @return RedirectResponse|View
     */
    public function demo(Solution $solution, string $version) {
        if (!$solutionVersion = $solution->version($version)) {
            return redirect(route('index'))->with('error', 'Lösung existiert nicht.');
        }

        // Aufgrund der möglichen Weiterleitungen hier die Flash-Messages erneut setzen.
        session()->reflash();

        $ref = request('ref') ?? request()->query('ref', '');

        if (!$demo = $solutionVersion->runningUserDemo()) {
            return redirect(route('solution.start_demo', ['solution' => $solution, 'version' => $solutionVersion->version]) . '?ref=' . $ref);
        }

        // Demo existiert bereits, es wird zur Demo weitergeleitetet
        return redirect(route('demo.show', ['demo' => $demo]) . '?ref=' . $ref);
    }

    /**
     * Eine neue Demo-Simulation starten.
     * @param Solution $solution
     * @param string $version
     * @return Application|View
     */
    public function startDemo(Solution $solution, string $version) {
        if (!$solutionVersion = $solution->version($version)) {
            return redirect(ReferrerRedirector::to(base64_decode(request('ref')), $solution->authorProfileSolutionPath()))
                ->with('error', 'Version existiert nicht.');
        }

        $author = $solutionVersion->solution->author;
        $organisation = $author instanceof Organisation ? $author : null;

        // Falls diese URL manuell aufgerufen wird, obwohl bereits eine Simulation existiert.
        if ($solutionVersion->runningUserDemo() instanceof Demo) {
            return redirect(route('solution.demo', ['solution' => $solution, 'version' => $solutionVersion->version]));
        }

        // Zu den Demo-Optionen weiterleiten
        return view('solutions.start-demo', [
            'solutionVersion' => $solutionVersion,
            'name' => $solutionVersion->solution->full_namespace . ' - Demo starten',
            'organisation' => $organisation
        ]);
    }

    /**
     * Demo einer Lösung aus einer öffentlichen Anzeige starten.
     * @param string $namespace
     * @param string $identifier
     * @param string $version
     * @return Application|View
     */
    public function publicDemo(string $namespace, string $identifier, string $version) {
        $solution = Solution::whereFullNamespace($namespace . '/' . $identifier)->firstOrFail();

        if (!$solutionVersion = $solution->publicVersions($version)) {
            return redirect($solution->publicPath())->with('error', 'Version existiert nicht.');
        }

        // Aufgrund der möglichen Weiterleitungen hier die Flash-Messages erneut setzen.
        session()->reflash();

        $ref = request('ref') ?? request()->query('ref', '');

        if (!$demo = $solutionVersion->runningUserDemo()) {
            return redirect(route('solution.detail.start_demo', ['namespace' => $solution->namespace, 'identifier' => $solution->identifier, 'version' => $solutionVersion->version]) . '?ref=' . $ref);
        }

        // Demo existiert bereits, es wird zur Demo weitergeleitetet
        return redirect(route('demo.show', ['demo' => $demo]) . '?ref=' . $ref);
    }

    /**
     * Eine neue Demo-Simulation aus einer öffentlichen Lösungsanzeige starten.
     * @param string $namespace
     * @param string $identifier
     * @param string $version
     * @return Application|View
     */
    public function publicStartDemo(string $namespace, string $identifier, string $version) {
        $solution = Solution::whereFullNamespace($namespace . '/' . $identifier)->firstOrFail();

        if (!$solutionVersion = $solution->publicVersions($version)) {
            return redirect($solution->publicPath())->with('error', 'Version existiert nicht.');
        }

        $author = $solutionVersion->solution->author;
        $organisation = $author instanceof Organisation ? $author : null;

        // Falls diese URL manuell aufgerufen wird, obwohl bereits eine Simulation existiert.
        if (($demo = $solutionVersion->runningUserDemo()) instanceof Demo) {
            return redirect(route('solution.detail.demo', ['namespace' => $solution->namespace, 'identifier' => $solution->identifier, 'version' => $demo->solutionVersion->version]));
        }

        // Zu den Demo-Optionen weiterleiten
        return view('solutions.start-demo', [
            'solutionVersion' => $solutionVersion,
            'name' => $solutionVersion->solution->full_namespace . ' - Demo starten',
            'organisation' => $organisation
        ]);
    }

    /**
     * View für die Konfiguration der Lösung
     * @param Solution $solution
     * @return Application|View
     */
    public function config(Solution $solution) {
        $solution->latestVersion->licenseOwnerHasAllProcessLicenses(Organisation::whereNamespace('allisa')->first());

        if ($solution->author instanceof User) {
            $queryGraphsUrl = route('api.user.process_versions', ['meta' => 'meta']);
        }
        else {
            $queryGraphsUrl = route('api.organisation.process_versions', [
                'organisation' => $solution->author,
                'meta' => 'meta'
            ]);
        }

        $role = auth()->user()->roleWithin($solution->author);
        $canUpdateSolution = $solution->author instanceof User || $role?->can('solutions.update');
        $canDeleteSolution = $solution->author instanceof User || $role?->can('solutions.delete');
        $canCompleteVersion = $solution->author instanceof User || $role?->can('solution_version.create');

        return view('solutions.config', [
            'solution' => new SolutionWithTags($solution),
            'solutionVersion' => $solution->latestVersion,
            'processTypes' => $solution->latestVersion->processTypes(),
            'mainDemo' => $solution->mainDemo(),
            'hasPublishedVersion' => $solution->hasPublishedVersion(),
            'canUpdateSolution' => $canUpdateSolution,
            'canDeleteSolution' => $canDeleteSolution,
            'canCompleteVersion' => $canCompleteVersion,
            'urls' => [
                'update_solution' => route('api.solution.update', $solution),
                'update_solution_version' => route('api.solution_version.update', $solution->latestVersion),
                'query_graphs' => $queryGraphsUrl
            ]
        ]);
    }

    /**
     * Anzeige des Solution-Konfigurators
     * @param Solution $solution
     * @return Application|View
     */
    public function edit(Solution $solution) {
        $role = auth()->user()->roleWithin($solution->author);
        $canUpdateSolution = $solution->author instanceof User || $role?->can('solutions.update');
        $canDeleteSolution = $solution->author instanceof User || $role?->can('solutions.delete');
        $canCompleteVersion = $solution->author instanceof User || $role?->can('solution_version.create');

        return view('solutions.meta-data', [
            'solution' => new SolutionWithTags($solution),
            'mainDemo' => $solution->mainDemo(),
            'hasPublishedVersion' => $solution->hasPublishedVersion(),
            'canUpdateSolution' => $canUpdateSolution,
            'canDeleteSolution' => $canDeleteSolution,
            'canCompleteVersion' => $canCompleteVersion,
            'urls' => [
                'update_solution' => route('api.solution.update', $solution),
            ]
        ]);
    }

    /**
     * Eine Solution-Version zu einem Allisa System exportieren.
     * @param Solution $solution
     * @param string $version
     * @return Application|View
     */
    public function showSync(Solution $solution, string $version) {
        if (!$solutionVersion = $solution->version($version)) {
            return redirect(route('index'))->with('error', 'Prozess existiert nicht.');
        }

        if ($solution->authoredByOrganisation()) {
            $createSystemRoute = route('organisation.settings.system.create', $solution->author);
        }
        else {
            $createSystemRoute = route('settings.system.create');
        }

        return view('solution-versions.sync', [
            'createSystemRoute' => $createSystemRoute,
            'solutionVersion' => $solutionVersion,
            'systems' => $solutionVersion->solution->author->systems()->with('synchronizations')->get(),
        ]);
    }

    /**
     * Eine Solution-Version fertigstellen.
     * @param Solution $solution
     * @return Application|Factory|View|RedirectResponse
     */
    public function complete(Solution $solution) {
        $solutionVersion = $solution->latestVersion;
        $nextVersion = '0.0.1';

        if ($solution->latestPublishedVersion) {
            $parts = explode('.', $solution->latestPublishedVersion->version);
            $lastPart = (int) ($parts[2]);
            $lastPart++;
            $nextVersion = implode('.', [$parts[0], $parts[1], $lastPart]);
        }

        // Prüfen ob Version bereits publiziert wurde
        if ($solutionVersion->published_at) {
            return back()->with('warning', 'Die Version wurde bereits veröffentlicht.');
        }

        return view('solution-versions.complete', [
            'solutionVersion' => $solutionVersion,
            'solution' => $solutionVersion->solution,
            'processVersions' => $solutionVersion->processVersions(),
            'nextVersion' => $nextVersion,
            'hasPrivateProcess' => $solutionVersion->hasPrivateProcess(),
            'name' => 'Fertigstellen - ' . $solutionVersion->solution->full_namespace
        ]);
    }

    /**
     * Anzeige zum Löschen einer Solution inklusive aller Versionen.
     * @param Solution $solution
     * @return Application|View
     */
    public function delete(Solution $solution) {
        if ($solution->hasPublishedVersion()) {
            return redirect(route('solution.archive', $solution));
        }

        $role = auth()->user()->roleWithin($solution->author);
        $canUpdateSolution = $solution->author instanceof User || $role?->can('solutions.update');
        $canDeleteSolution = $solution->author instanceof User || $role?->can('solutions.delete');
        $canCompleteVersion = $solution->author instanceof User || $role?->can('solution_version.create');

        return view('solutions.delete', [
            'mainDemo' => $solution->mainDemo(),
            'solution' => $solution->load('versions'),
            'canUpdateSolution' => $canUpdateSolution,
            'canDeleteSolution' => $canDeleteSolution,
            'canCompleteVersion' => $canCompleteVersion,
            'title' => 'Löschen - ' . $solution->full_namespace
        ]);
    }

    /**
     * Anzeige zum Archivieren einer Solution inklusive aller Versionen.
     * @param Solution $solution
     * @return Application|Factory|View
     */
    public function archive(Solution $solution) {
        $role = auth()->user()->roleWithin($solution->author);
        $canUpdateSolution = $solution->author instanceof User || $role?->can('solutions.update');
        $canDeleteSolution = $solution->author instanceof User || $role?->can('solutions.delete');


        return view('solutions.archive', [
            'solution' => $solution->load('versions'),
            'canUpdateSolution' => $canUpdateSolution,
            'canDeleteSolution' => $canDeleteSolution,
            'title' => 'Archivieren - ' . $solution->full_namespace
        ]);
    }

    /**
     * Alle Versionen einer Solution.
     * @param Solution $solution
     * @return Application|Factory|View
     */
    public function versions(Solution $solution) {
        $solution->loadProcessVersionsOfVersions(['synchronizations', 'synchronizations.system']);

        $role = auth()->user()->roleWithin($solution->author);
        $canUpdateSolution = $solution->author instanceof User || $role?->can('solutions.update');
        $canDeleteSolution = $solution->author instanceof User || $role?->can('solutions.delete');
        $canCompleteVersion = $solution->author instanceof User || $role?->can('solution_versions.create');

        return view('solutions.versions', [
            'solution' => $solution,
            'mainDemo' => $solution->mainDemo(),
            'latestPublicSolutionVersion' => $solution->publicVersions('latest'),
            'systems' => $solution->author->systems,
            'canUpdateSolution' => $canUpdateSolution,
            'canDeleteSolution' => $canDeleteSolution,
            'canCompleteVersion' => $canCompleteVersion,
            'title' => 'Versionen - ' . $solution->full_namespace
        ]);
    }

    /**
     * Löschen einer Lösung.
     * @param DeleteSolution $request
     * @param Solution $solution
     * @return RedirectResponse
     */
    public function destroy(DeleteSolution $request, Solution $solution) {
        $request->validated();

        try {
            if ($solution->visibility === Visibility::Public->value) {
                $solution->updateVisibility(Visibility::Hidden->value);
            }

            // Wenn eine fertiggestellte Version bereits existiert, wird der Prozess lediglich "archiviert".
            $solution->hasPublishedVersion() ? $solution->delete() : $solution->forceDelete();
        }
        catch (Exception) {
            // Ignore
        }

        return redirect($solution->authorProfileSolutionPath())->with('success', 'Lösung gelöscht.');
    }

}
