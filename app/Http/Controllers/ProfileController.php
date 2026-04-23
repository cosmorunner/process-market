<?php

namespace App\Http\Controllers;

use App\Enums\Visibility;
use App\Http\Requests\CloseAccount;
use App\Http\Requests\QueryList;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\Simulation;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

/**
 * Class ProfileController
 * @package App\Http\Controllers
 */
class ProfileController extends Controller {

    /**
     * Profile of the user
     * @param QueryList $request
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function processes(QueryList $request) {
        $user = Auth::user();
        $archived = $request['archived'] && $request['archived'] === "1";
        $query = $archived
            ? $user->processes()->onlyTrashed()->with(['tags', 'latestVersion'])
            : $user->processes()
                ->withoutTrashed()
                ->with(['tags', 'latestVersion']);

        // Sort
        $sort = $request->query('sort', 'lastChange_desc');
        [$sortField, $sortDirection] = explode('_', $sort);

        switch ($sortField) {
            case 'lastChange':
                $query->orderBy('updated_at', $sortDirection);
                break;
            case 'alphabetically':
                $query->orderBy('title', $sortDirection);
                break;
            case 'complexity':
                $query->leftJoin('process_versions', 'processes.id', '=', 'process_versions.process_id')
                    ->where('process_versions.version', '=', 'develop')
                    ->orderBy('process_versions.complexity_score', $sortDirection)
                    ->select('processes.*');
                break;
            default:
                $query->orderBy('updated_at', 'desc');
                break;
        }

        $processes = $query->get();

        // Search
        $searchTerm = $request->query('search');
        if ($searchTerm) {
            $processes = $processes->filter(fn(Process $process) => str_contains(strtolower($process->title), strtolower($searchTerm)));
        }

        return view('profile.processes', [
            'user' => $user,
            'runningSimulations' => $user->runningUserSimulations(),
            'runningDemos' => $user->runningUserDemos(),
            'processes' => $processes,
            'title' => $user->namespace . ' - Prozesse',
            'archived' => $archived
        ]);
    }

    /**
     * Licenses acquired by the user.
     * @return Factory|\Illuminate\View\View
     */
    public function licenses() {
        $user = Auth::user();
        $filter = request('f', 'processes');
        $licenses = $user->licenses()->with('resource')->get();

        return view('profile.licenses', [
            'user' => $user,
            'runningSimulations' => $user->runningUserSimulations(),
            'runningDemos' => $user->runningUserDemos(),
            'processLicenses' => $user->processLicenses,
            'solutionLicenses' => $user->solutionLicenses,
            'licenses' => $licenses,
            'filter' => $filter,
            'title' => $user->namespace . ' - Lizenzen'
        ]);
    }

    /**
     * Solutions created by the user.
     * @return Factory|View
     */
    public function solutions() {
        $user = Auth::user();

        return view('profile.solutions', [
            'user' => $user,
            'runningSimulations' => $user->runningUserSimulations(),
            'runningDemos' => $user->runningUserDemos(),
            'solutions' => $user->solutions()->withoutTrashed()->with(['tags'])->latest('updated_at')->get(),
            'title' => 'Lösungen - ' . $user->namespace,
        ]);
    }


    /**
     * View for creating a demo Allisa instance.
     * @return Application|Factory|View|RedirectResponse
     */
    public function createAllisaDemo() {
        $user = Auth::user();

        if ($user->hasDemo()) {
            return redirect()
                ->to(route('profile.processes'))
                ->with('info', 'Sie haben bereits eine Allisa Demo Plattform angelegt.');
        }

        return view('profile.create-allisa-demo', [
            'identifier' => $user->namespace,
            'searchEndpoint' => route('api.allisa_platform_exists', ''),
            'storeEndpoint' => route('api.store_allisa_platform'),
            'systemsRoute' => route('settings.systems'),
            'profileRoute' => route('profile.processes'),
            'allisaPrivacy' => asset('documents/allisa-privacy.pdf'),
            'allisaTerms' => asset('documents/allisa-terms.pdf'),
        ]);
    }

    /**
     * Deactivate account
     * @param CloseAccount $request
     * @return Application|RedirectResponse|Redirector
     * @throws GuzzleException
     */
    public function closeAccount(CloseAccount $request) {
        $request->validated();
        $user = Auth::user();
        $user->processes->each(fn(Process $process) => $process->updateVisibility(Visibility::Private->value));

        // End running simulations.
        $user->processes->map(fn(Process $process) => $process->runningSimulations())
            ->flatten(1)
            ->each(fn(Simulation $simulation) => $simulation->finish());

        // Delete all accesses to organisations
        $user->organisations->each(function (Organisation $organisation) use ($user) {
            $user->accesses()
                ->where('resource_type', Organisation::class)
                ->where('resource_id', $organisation->id)
                ->delete();
        });

        Auth::logout();

        try {
            $user->delete();

            return redirect(route('login'))->with('success', 'Sie haben ihr Konto deaktiviert. Auf Wiedersehen!');
        }
        catch (Exception) {
            // Ignore;
        }

        return redirect(route('login'))->with('success', 'Sie haben ihr Konto deaktiviert. Auf Wiedersehen!');
    }
}
