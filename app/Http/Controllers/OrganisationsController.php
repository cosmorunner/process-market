<?php

namespace App\Http\Controllers;

use App\Enums\Visibility;
use App\Http\Requests\DeleteOrganisation;
use App\Http\Requests\DeleteOrganisationMember;
use App\Http\Requests\ExitOrganisation;
use App\Http\Requests\QueryList;
use App\Http\Requests\StoreOrganisation;
use App\Http\Requests\StoreOrganisationInvitation;
use App\Http\Requests\StoreSystem;
use App\Http\Requests\UpdateOrganisationData;
use App\Http\Requests\UpdateOrganisationMember;
use App\Mail\OrganisationInvitationWithExistingUser;
use App\Mail\OrganisationInvitationWithoutExistingUser;
use App\Models\Invitation;
use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Process;
use App\Models\Role;
use App\Models\Simulation;
use App\Models\System;
use App\Models\User;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Class UsersController
 * @package App\Http\Controllers
 */
class OrganisationsController extends Controller {

    /**
     * Public page of an organization.
     * @param Organisation $organisation
     * @param QueryList $request
     * @return Application|Factory|View
     */
    public function show(Organisation $organisation, QueryList $request) {
        $processesCollection = $organisation->publicProcesses();

        // Search
        $searchTerm = $request->query('search');
        if ($searchTerm) {
            $processesCollection = $processesCollection->filter(fn($process) => str_contains(strtolower($process->title), strtolower($searchTerm)));
        }

        // Sort
        $sort = $request->query('sort', 'lastChange_desc');
        [$sortField, $sortDirection] = explode('_', $sort);

        $processesCollection = match ($sortField) {
            'lastChange' => $sortDirection === 'asc' ? $processesCollection->sortBy('updated_at') : $processesCollection->sortByDesc('updated_at'),
            'alphabetically' => $sortDirection === 'asc' ? $processesCollection->sortBy('title') : $processesCollection->sortByDesc('title'),
            'complexity' => $processesCollection->sortBy(fn($process) => $process->latestPublishedVersion?->complexity_score, SORT_REGULAR, $sortDirection === 'desc'),
            default => $processesCollection->sortByDesc('updated_at'),
        };

        return view('organisations.show', [
            'organisation' => $organisation,
            'processes' => $processesCollection,
            'title' => $organisation->name . ' - ' . $organisation->namespace
        ]);
    }

    /**
     * Processes of an organization.
     * @param Organisation $organisation
     * @param QueryList $request
     * @return Application|Factory|View
     */
    public function processes(Organisation $organisation, QueryList $request) {
        $archived = $request['archived'] && $request['archived'] === "1";
        $query = $archived ? $organisation->processes()->onlyTrashed()->with([
            'tags',
            'latestVersion'
        ]) : $organisation->processes()->withoutTrashed()->with(['tags', 'latestVersion']);

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

        return view('organisations.profile.processes', [
            'organisation' => $organisation,
            'processes' => $processes,
            'runningSimulations' => $organisation->runningUserSimulations(),
            'runningDemos' => $organisation->runningUserDemos(),
            'title' => 'Profil - ' . $organisation->name,
            'role' => auth()->user()->roleWithin($organisation),
            'archived' => $archived
        ]);
    }

    /**
     * Solutions of an organization.
     * @param Organisation $organisation
     * @return Application|Factory|View
     */
    public function solutions(Organisation $organisation) {
        return view('organisations.profile.solutions', [
            'organisation' => $organisation,
            'solutions' => $organisation->solutions()->withoutTrashed()->with(['tags'])->latest('updated_at')->get(),
            'runningSimulations' => $organisation->runningUserSimulations(),
            'runningDemos' => $organisation->runningUserDemos(),
            'title' => 'Lösungen - ' . $organisation->namespace,
            'role' => auth()->user()->roleWithin($organisation)
        ]);
    }

    /**
     * Licenses of an organization.
     * @param Organisation $organisation
     * @return Application|Factory|View
     */
    public function licenses(Organisation $organisation) {
        $filter = request('f', 'processes');
        $licenses = $organisation->licenses()->with('resource')->get();

        return view('organisations.profile.licenses', [
            'organisation' => $organisation,
            'runningSimulations' => $organisation->runningUserSimulations(),
            'runningDemos' => $organisation->runningUserDemos(),
            'processLicenses' => $organisation->processLicenses,
            'solutionLicenses' => $organisation->solutionLicenses,
            'licenses' => $licenses,
            'filter' => $filter,
            'title' => $organisation->namespace . ' - Lizenzen',
            'role' => auth()->user()->roleWithin($organisation)
        ]);
    }

    /**
     * Members of the organization
     * @param Organisation $organisation
     * @return Application|Factory|View
     */
    public function members(Organisation $organisation) {
        /* @var User $user */
        $user = auth()->user();
        $accesses = $organisation->accesses()->with(['role', 'recipient'])->get();
        $userRole = $organisation->roleOf($user);

        return view('organisations.profile.members', [
            'organisation' => $organisation,
            'accesses' => $accesses,
            'userRole' => $userRole,
            'user' => $user,
            'runningSimulations' => $organisation->runningUserSimulations(),
            'runningDemos' => $organisation->runningUserDemos(),
            'title' => 'Mitglieder - ' . $organisation->name,
            'role' => auth()->user()->roleWithin($organisation)
        ]);
    }

    /**
     * Transfer ownership of an organisation.
     * @param Organisation $organisation
     * @param User $user
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Redirector
     */
    public function transferOwner(Organisation $organisation, User $user) {
        /* @var User $user */
        $loginUser = auth()->user();

        if (!$organisation->roleOf($loginUser)->isOwner()) {
            return back()->with('error', 'Sie sind nicht Eigentümer');
        }

        $organisation->updateAccess($user, $organisation->ownerRole());
        $organisation->updateAccess($loginUser, $organisation->adminRole());

        return redirect(route('organisation.members', ['organisation' => $organisation]))->with('success', 'Eingetümer Rolle wurde übertragen');
    }

    /**
     * View for creating an organization.
     * @return Application|Factory|View
     */
    public function create() {
        return view('organisations.create', [
            'title' => 'Organisation erstellen'
        ]);
    }

    /**
     * Create a new organization.
     * @param StoreOrganisation $request
     * @return RedirectResponse
     */
    public function store(StoreOrganisation $request) {
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()
            ->withDefaultRoles()
            ->withOwner(Auth::user())
            ->create(array_merge($request->validated(), ['city' => '']));

        return redirect()->to($organisation->profileProcessesPath());
    }

    /**
     * Basic settings of an organization.
     * @param Organisation $organisation
     * @return Application|Factory|View
     */
    public function data(Organisation $organisation) {
        return view('organisations.settings.data', [
            'organisation' => $organisation,
            'title' => 'Daten - ' . $organisation->name
        ]);
    }

    /**
     * Account settings of an organization.
     * @param Organisation $organisation
     * @return Application|Factory|View
     */
    public function account(Organisation $organisation) {
        return view('organisations.settings.account', [
            'organisation' => $organisation,
            'title' => 'Konto - ' . $organisation->name
        ]);
    }

    /**
     * Associated Allisa systems.
     * @param Organisation $organisation
     * @return Application|Factory|View
     */
    public function systems(Organisation $organisation) {
        return view('organisations.settings.systems', [
            'organisation' => $organisation,
            'systems' => $organisation->systems,
            'title' => 'Plattformen - ' . $organisation->name
        ]);
    }

    /**
     * Add a system for organization.
     * @param Organisation $organisation
     * @return Application|Factory|View
     */
    public function createSystem(Organisation $organisation) {
        return view('organisations.settings.create_system', [
            'organisation' => $organisation,
            'title' => 'Plattform hinzufügen - ' . $organisation->name
        ]);
    }

    /**
     * Add Allisa Platform.
     * @param StoreSystem $request
     * @param Organisation $organisation
     * @return Application|RedirectResponse|Redirector
     * @throws GuzzleException
     */
    public function storeSystem(StoreSystem $request, Organisation $organisation) {
        $validated = $request->validated();
        $url = rtrim($validated['url'], '/');

        try {
            System::register($url, $validated['name'], $organisation, $validated['client_id'], $validated['client_secret']);
        }
        catch (ConnectException) {
            return back()
                ->with('error', 'Die URL ist inkorrekt. Bitte stellen Sie sicher, dass die URL auf die Startseite eines Allisa-Systems verweist, z.B. https://kunde.example.com.')
                ->withInput([
                    'name' => $validated['name'],
                    'client_id' => $validated['client_id'],
                    'client_secret' => $validated['client_secret']
                ]);
        }
        catch (ClientException $exception) {
            if ($exception->getCode() === Response::HTTP_UNAUTHORIZED) {
                return back()->with('error', 'Die Client-Id oder das Client-Secret sind ungültig.')->withInput([
                    'name' => $validated['name'],
                    'url' => $validated['url']
                ]);
            }
            else {
                report($exception);

                return back()->with('error', 'Ein unerwarteter Fehler ist aufgetreten, bitte versuchen Sie es erneut.');
            }
        }
        catch (ServerException) {
            return back()->with('error', 'Bei der Allisa Plattform ist es zu einem Fehler gekommen. Bitte wenden Sie sich an den administrativen Support der Allisa Plattform.');
        }

        return redirect(route('organisation.settings.systems', $organisation))->with('success', 'Allisa Plattform registriert.');
    }

    /**
     * Delete Allisa platform.
     * @param Organisation $organisation
     * @param System $system
     * @return Application|RedirectResponse|Redirector
     */
    public function deleteSystem(Organisation $organisation, System $system) {
        try {
            $system->delete();
        }
        catch (Exception) {
            // Ignore
        }

        return redirect(route('organisation.settings.systems', $organisation))->with('success', 'Allisa Plattform entfernt.');
    }

    /**
     * Change membership of a member.
     * @param Organisation $organisation
     * @param User $user
     * @return Application|Factory|View
     */
    public function editMember(Organisation $organisation, User $user) {
        if (!$organisation->roleOf($user)) {
            return redirect(route('organisation.members', $organisation))->with('success', 'Mitglied existiert nicht.');
        }

        /* @var Permission $permission */
        $ownerPermissions = Permission::whereIn('ident', config('roles.owner.permissions', []))->get();
        $adminPermissions = $ownerPermissions->filter(fn($permission) => in_array($permission->ident, config('roles.admin.permissions', [])));
        $managerPermissions = $ownerPermissions->filter(fn($permission) => in_array($permission->ident, config('roles.manager.permissions', [])));
        $developerPermissions = $ownerPermissions->filter(fn($permission) => in_array($permission->ident, config('roles.developer.permissions', [])));
        $reporterPermissions = $ownerPermissions->filter(fn($permission) => in_array($permission->ident, config('roles.reporter.permissions', [])));

        return view('organisations.members.edit', [
            'organisation' => $organisation,
            'user' => $user,
            'userRole' => $organisation->roleOf($user),
            'ownerPermissions' => $ownerPermissions,
            'adminPermissions' => $adminPermissions,
            'managerPermissions' => $managerPermissions,
            'developerPermissions' => $developerPermissions,
            'reporterPermissions' => $reporterPermissions,
            'title' => 'Mitglied bearbeiten - ' . $organisation->name
        ]);
    }

    /**
     * Save changes to a membership.
     * @param UpdateOrganisationMember $request
     * @param Organisation $organisation
     * @param User $user
     * @return Application
     */
    public function updateMember(UpdateOrganisationMember $request, Organisation $organisation, User $user) {
        /* @var Role $role */
        $validated = $request->validated();
        $role = $organisation->roles()->firstWhere('id', '=', $validated['role'] ?? null);

        if (!$role->isOwner()) {
            $organisation->updateAccess($user, $role);
        }

        return redirect(route('organisation.members', ['organisation' => $organisation]))->with('success', 'Mitgliedschaft wurde aktualisiert.');
    }

    /**
     * Change membership of a member.
     * @param Organisation $organisation
     * @return Application|Factory|View
     * @noinspection PhpUnused
     */
    public function inviteMember(Organisation $organisation) {
        /* @var Permission $permission */
        $ownerPermissions = Permission::whereIn('ident', config('roles.owner.permissions', []))->get();
        $adminPermissions = $ownerPermissions->filter(fn($permission) => in_array($permission->ident, config('roles.admin.permissions', [])));
        $managerPermissions = $ownerPermissions->filter(fn($permission) => in_array($permission->ident, config('roles.manager.permissions', [])));
        $developerPermissions = $ownerPermissions->filter(fn($permission) => in_array($permission->ident, config('roles.developer.permissions', [])));
        $reporterPermissions = $ownerPermissions->filter(fn($permission) => in_array($permission->ident, config('roles.reporter.permissions', [])));

        return view('organisations.members.invite', [
            'organisation' => $organisation,
            'title' => 'Einladen - ' . $organisation->name,
            'ownerPermissions' => $ownerPermissions,
            'adminPermissions' => $adminPermissions,
            'managerPermissions' => $managerPermissions,
            'developerPermissions' => $developerPermissions,
            'reporterPermissions' => $reporterPermissions,
        ]);
    }

    /**
     * Leaving the organization
     * @param ExitOrganisation $request
     * @param Organisation $organisation
     * @return Application
     */
    public function exit(ExitOrganisation $request, Organisation $organisation) {
        $request->validated();
        $organisation->removeUser(Auth::user());

        return redirect(route('profile.processes'))->with('success', 'Sie sind aus der Organisation ausgetreten.');
    }

    /**
     * Disband an organization.
     * @param DeleteOrganisation $request
     * @param Organisation $organisation
     * @return Application
     * @throws Throwable
     */
    public function delete(DeleteOrganisation $request, Organisation $organisation) {
        $request->validated();

        try {
            DB::beginTransaction();

            // The other relations are deleted using the model observer.
            $organisation->processes->each(fn(Process $process) => $process->updateVisibility(Visibility::Private->value));

            // End running simulations.
            $organisation->processes->map(fn(Process $process) => $process->runningSimulations())
                ->flatten(1)
                ->each(fn(Simulation $simulation) => $simulation->finish());

            $organisation->invitations()->delete();

            // Organisation is not deleted, only all accesses are removed.
            $organisation->removeAllAccesses();

            DB::commit();
        }
        catch (Exception) {
            DB::rollBack();
        }

        return redirect(route('profile.processes'))->with('success', 'Die Organisation wurde erfolgreich aufgelöst.');
    }

    /**
     * Invite an external, unregistered person to the system and the group.
     * @param StoreOrganisationInvitation $request
     * @param Organisation $organisation
     * @return RedirectResponse|Redirector
     */
    public function storeInvitation(StoreOrganisationInvitation $request, Organisation $organisation) {
        $validated = $request->validated();
        $email = strtolower($validated['email']);
        $roleId = $validated['role'];

        if (Role::whereId($roleId)->firstOrFail()->isOwner()) {
            return back()->with('error', 'Kann keine weiteren Eigentümer einladen');
        }

        $invitation = Invitation::create([
            'email' => $email,
            'resource_type' => Organisation::class,
            'resource_id' => $organisation->id,
            'role_id' => $roleId,
            'expires_at' => Carbon::now()->addWeek(),
            'creator_id' => Auth::user()->id
        ]);

        if (User::whereEmail($email)->exists()) {
            Mail::send(new OrganisationInvitationWithExistingUser($invitation));
        }
        else {
            Mail::send(new OrganisationInvitationWithoutExistingUser($invitation));
        }

        return redirect(route('organisation.members', $organisation))->with('success', 'Die Einladung wurde an "' . $email . '" versendet.');
    }

    /**
     * Resend an invitation to the organization.
     * @param Organisation $organisation
     * @param Invitation $invitation
     * @return Application|RedirectResponse|Redirector
     * @throws Exception
     */
    public function resendInvitation(Organisation $organisation, Invitation $invitation) {
        $user = User::whereEmail($invitation->email)->first();

        if ($user instanceof User && $organisation->isMember($user)) {
            $invitation->delete();

            return redirect(route('organisation.members', $organisation))->with('info', 'Einladung ist nicht mehr gültig.');
        }
        else if ($user instanceof User && !$organisation->isMember($user)) {
            Mail::send(new OrganisationInvitationWithExistingUser($invitation));
        }
        else {
            Mail::send(new OrganisationInvitationWithoutExistingUser($invitation));
        }

        $invitation->renewExpiry();

        return redirect(route('organisation.members', $organisation))->with('success', 'Die Einladung wurde erneut an "' . $invitation->email . '" versendet.');
    }

    /**
     * Delete an invitation to the organization.
     * @param Organisation $organisation
     * @param Invitation $invitation
     * @return Application|RedirectResponse|Redirector
     */
    public function deleteInvitation(Organisation $organisation, Invitation $invitation) {
        try {
            $invitation->delete();
        }
        catch (Exception) {
            // Ingore
        }

        return redirect(route('organisation.members', $organisation))->with('success', 'Die Einladung für "' . $invitation->email . '" wurde entfernt.');
    }

    /**
     * Remove member from the organization
     * @param DeleteOrganisationMember $request
     * @param Organisation $organisation
     * @param User $user
     * @return Application
     */
    public function deleteMember(DeleteOrganisationMember $request, Organisation $organisation, User $user) {
        $request->validated();
        $organisation->removeUser($user);

        return redirect(route('organisation.members', $organisation))->with('success', 'Benutzer wurde entfernt.');
    }

    /**
     * Change basic data of an organization.
     * @param UpdateOrganisationData $request
     * @param Organisation $organisation
     * @return RedirectResponse|Redirector
     * @throws Throwable
     */
    public function updateData(UpdateOrganisationData $request, Organisation $organisation) {
        $validated = $request->validated();
        $organisation->update($validated);

        return redirect(route('organisation.settings.data', $organisation))->with('success', 'Daten aktualisiert.');
    }
}
