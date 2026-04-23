<?php

namespace App\Http\Controllers;

use App\Interfaces\Accessible;
use App\Models\Invitation;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class InvitationsController
 * @package App\Http\Controllers
 */
class InvitationsController extends Controller {

    /**
     * @return RedirectResponse|Redirector
     */
    public function index() {
        return redirect(route('login'));
    }

    /**
     * Eine Einladung annehmen.
     * @param Invitation $invitation
     * @return Application|RedirectResponse|Redirector
     * @throws Throwable
     */
    public function accept(Invitation $invitation) {
        if ($invitation->isInvalid()) {
            $invitation->delete();

            return redirect(route('login'))->with('info', __('exceptions.invitation_is_invalid'));
        }

        DB::beginTransaction();

        try {
            $user = User::firstWhere('email', '=', $invitation->email);

            if (!$user) {
                return redirect(route('register', ['invitation' => $invitation]));
            }

            // Einladung zu einer Gruppe oder Prozess-Instanz.
            if ($user instanceof User && $invitation->resource instanceof Accessible) {
                $invitation->resource->addUser($user, $invitation->role);
            }

            // Alle Einladungen mit dieser E-Mail Adresse löschen.
            Invitation::whereEmail($invitation->email)->delete();

            DB::commit();
        }
        catch (Exception) {
            DB::rollBack();

            return back()->with('error', __('exceptions.default_message'));
        }

        if (Auth::check()) {
            if (Auth::user()->id === $user->id) {
                return redirect(route('organisation.processes', ['organisation' => $invitation->resource]))->with('success', 'Einladung erfolgreich angenommen.');
            }
            else {
                Auth::logout();
            }
        }

        return redirect(route('login'))->with('success', 'Einladung erfolgreich angenommen.');
    }

}
