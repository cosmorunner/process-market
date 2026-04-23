<?php

namespace App\Http\Controllers\Auth;

use App\FlashMessages\MessageWithLinkButton;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

/**
 * Class VerificationController
 * @package App\Http\Controllers\Auth
 */
class VerificationController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     * @var string
     */
    protected $redirectTo = 'login';

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct() {
        $this->middleware('auth')->only('show');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Überschrieben aus VerifiesEmail-Trait, weil standardgemäß der Benutzer eingeloggt sein muss
     * um zu verifizieren (siehe VerifiesEmails "$request->user()->getKey()").
     * E-Mail verifizieren mittels Verifizierungslink.
     * @param Request $request
     * @return RedirectResponse|Response|Redirector
     * @throws AuthorizationException
     */
    public function verify(Request $request) {
        $user = User::find($request->route('id'));

        if (!$user) {
            return $request->wantsJson() ? new Response('', 404) : redirect($this->redirectPath())->with('error', 'Der Benutzer existiert nicht.');
        }

        if (!hash_equals((string)$request->route('id'), (string)$user->id)) {
            throw new AuthorizationException;
        }

        if (!hash_equals((string)$request->get('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return $request->wantsJson() ? new Response('', 204) : redirect($this->redirectPath());
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        if ($response = $this->verified($request)) {
            return $response;
        }

        return $request->wantsJson() ? new Response('', 204) : redirect($this->redirectPath())->with('success', 'Erfolgreich verifiziert. Sie können sich jetzt anmelden.');
    }

    /**
     * Überschrieben aus VerifiesEmail-Trait, weil standardgemäß der Benutzer eingeloggt sein muss
     * um zu verifizieren (siehe VerifiesEmails "$request->user()->getKey()").
     * @param Request $request
     * @return RedirectResponse|Response|Redirector
     */
    public function resend(Request $request) {
        $user = User::find($request->route('id'));

        if (!$user) {
            return $request->wantsJson() ? new Response('', 404) : redirect($this->redirectPath())->with('error', 'Der Benutzer existiert nicht.');
        }

        if ($user->hasVerifiedEmail()) {
            return $request->wantsJson() ? new Response('', 204) : redirect($this->redirectPath());
        }

        $user->sendEmailVerificationNotification();

        return $request->wantsJson() ? new Response('', 202) : redirect($this->redirectPath())
            ->with('info', new MessageWithLinkButton('Erneut gesendet. Bitte überprüfen Sie ihr E-Mail Postfach (evtl. auch den Spam-Ordner) auf einen Verifizierungslink.',
                route('verification.resend', ['id' => $user->id]), 'Erneut senden'));
    }
}
