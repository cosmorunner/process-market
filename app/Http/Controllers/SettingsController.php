<?php /** @noinspection PhpUnused */

namespace App\Http\Controllers;

use App\Http\Requests\StoreSystem;
use App\Http\Requests\UpdateAccountData;
use App\Http\Requests\UpdatePassword;
use App\Models\Organisation;
use App\Models\Setting;
use App\Models\System;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

/**
 * Class AccountController
 * @package App\Http\Controllers
 */
class SettingsController extends Controller {

    /**
     * Weiterleitung zu den Basis-Profileinstellungen.
     * @return Application|RedirectResponse|Redirector
     */
    public function index() {
        return redirect(route('settings.data'));
    }

    /**
     * Schnell-Update einer Benutzer-Einstellung
     * @return Application|RedirectResponse|Redirector
     */
    public function updateSetting(string $settingName) {
        $names = ['hide-allisa-promotion'];

        if(!in_array($settingName, $names)) {
            return back();
        }

        if($settingName === 'hide-allisa-promotion') {
            Setting::upsertSetting($settingName, true, auth()->user());
        }

        return back()->with('success', 'Gespeichert');
    }

    /**
     * Basis-Profileinstellungen
     * @return Application|Factory|View
     */
    public function data() {
        return view('settings.data', [
            'user' => Auth::user(),
            'title' => 'Daten - Einstellungen'
        ]);
    }

    /**
     * Zugehörige Organisationen.
     * @return Application|Factory|View
     */
    public function organisations() {
        $organisations = Auth::user()->organisations()->with('accesses')->get();

        return view('settings.organisations', [
            'organisations' => $organisations,
            'title' => 'Organisationen - Einstellungen'
        ]);
    }

    /**
     * Zugehörige Allisa-Systeme.
     * @return Application|Factory|View
     */
    public function systems() {
        $systems = Auth::user()->systems;

        return view('settings.systems', [
            'systems' => $systems,
            'title' => 'Plattformen - Einstellungen'
        ]);
    }

    /**
     * @return Application|Factory|View
     */
    public function createSystem() {
        return view('settings.create-system');
    }

    /**
     * Sicherheitseinstellungen
     * @return Application|Factory|View
     */
    public function security() {
        return view('settings.security', ['user' => Auth::user()]);
    }

    /**
     * Account-Einstellungen
     * @return Application|Factory|View
     */
    public function account() {
        return view('settings.account', ['user' => Auth::user()]);
    }

    /**
     * Allisa Plattform registrieren.
     * @param StoreSystem $request
     * @return Application|RedirectResponse|Redirector
     * @throws GuzzleException
     */
    public function storeSystem(StoreSystem $request) {
        $validated = $request->validated();
        $ownerId = $validated['owner_id'];
        $owner = Auth::user()->id === $ownerId ? Auth::user() : Organisation::find($ownerId);
        $url = rtrim($validated['url'], '/');

        try {
            System::register($url, $validated['name'], $owner, $validated['client_id'], $validated['client_secret']);
        }
        catch (ConnectException) {
            return back()->with('error', 'Die URL ist inkorrekt. Bitte stellen Sie sicher, dass die URL auf die Startseite eines Allisa-Systems verweist, z.B. https://kunde.example.com')->withInput([
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

        return redirect(route('settings.systems'))->with('success', 'Allisa Plattform registriert.');
    }

    /**
     * Allisa Plattform löschen.
     * @param System $system
     * @return Application|RedirectResponse|Redirector
     */
    public function deleteSystem(System $system) {
        try {
            $system->delete();
        }
        catch (Exception) {
            // Ignore
        }

        return redirect(route('settings.systems'))->with('success', 'Allisa Plattform entfernt.');
    }

    /**
     * @param UpdatePassword $request
     * @return RedirectResponse|Redirector
     */
    public function updatePassword(UpdatePassword $request) {
        $validated = $request->validated();
        $current = $validated['current_password'];
        $new = $validated['password'];
        $user = Auth::user();

        if (!Hash::check($current, $user->password)) {
            return back()->withErrors(['current_password' => 'Falsches Passwort.']);
        }

        // Neues Passwort speichern
        $user->password = Hash::make($new);
        $user->save();

        return redirect(route('settings.security'))->with('success', 'Passwort aktualisiert.');
    }

    /**
     * @param UpdateAccountData $request
     * @return RedirectResponse|Redirector
     * @throws Throwable
     */
    public function updateData(UpdateAccountData $request) {
        Auth::user()->update($request->validated());

        return redirect(route('settings.data'))->with('success', 'Daten aktualisiert.');
    }
}
