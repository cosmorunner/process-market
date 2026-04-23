<?php

namespace App\Http\Controllers\Auth;

use App\FlashMessages\MessageWithLinkButton;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Organisation;
use App\Models\User;
use App\Rules\EqualsInvitationEmail;
use App\Rules\ValidInvitation;
use App\Rules\ValidNamespaceIdentifierFormat;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = 'login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     * @return Application|Factory|View
     */
    public function showRegistrationForm() {
        $invitation = Invitation::find(request()->query('invitation')) ?? request()->query('invitation');

        // Möglicherweise wird eine Einladung angenommen.
        return view('auth.register', [
            'invitation' => $invitation
        ]);
    }

    /**
     * Überschrieben aus RegisterUsers Trait. Der Benutzer soll nicht automatisch eingeloggt werden.
     * Öffentliche Registrierung eines neuen Benutzers.
     * @param Request $request
     * @return RedirectResponse|Response|Redirector
     * @throws ValidationException
     */
    public function register(Request $request) {
        $this->validator($request->all())->validate();
        $invitation = Invitation::find(request('invitation'));
        $user = $this->create($request->all());

        event(new Registered($user));

        if ($invitation instanceof Invitation && $invitation->resource instanceof Organisation) {
            $this->acceptInvitation($user, $invitation);
        }

        // Beim Folgen einer Einladung muss die E-Mail Adresse nicht verifiziert werden.
        if ($invitation instanceof Invitation) {
            $info = 'Sie haben sich erfolgreich registriert und können sich jetzt einloggen.';
        }
        // Bestätigungsemail senden.
        else {
            $user->sendEmailVerificationNotification();
            $info = new MessageWithLinkButton('Wir haben Ihnen eine E-Mail gesendet. Bitte bestätigen Sie Ihre Registrierung, indem Sie auf den Link in der E-Mail klicken.',
                route('verification.resend', ['id' => $user->id]), 'Erneut senden');
        }

        return $request->wantsJson() ? new Response('', 201) :
            redirect($this->redirectPath())->with('info', $info);
    }

    /**
     * Überschrieben aus RegisterUsers Trait.
     * Nachdem der Benutzer registriert wurde.
     * @param User $user
     * @param Invitation|null $invitation
     */
    protected function acceptInvitation($user, Invitation $invitation = null) {
        $invitation->resource->addUser($user, $invitation->role);

        // Bei einer Einladung wird die E-Mail automatisch verifiziert.
        $user->markEmailAsVerified();

        try {
            $invitation->delete();
        } catch (Exception) {
            // Ignore
        }

        session()->flash('success', 'Sie sind der Organisation "' . $invitation->resource->name . '" beigetreten.');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
            'namespace' => ['required', 'string', 'min:3', 'max:20', new ValidNamespaceIdentifierFormat, 'unique:users,namespace', 'unique:organisations,namespace'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', new EqualsInvitationEmail(request('invitation'))],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invitation' => ['bail', 'nullable', 'uuid', 'exists:invitations,id', new ValidInvitation],
            'terms' => ['accepted']
        ], [], [
            'namespace' => 'Benutzername',
            'terms' => 'AGBs und Datenschutzerklärung'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return User
     */
    protected function create(array $data) {
        return User::forceCreate([
            'namespace' => $data['namespace'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
        ]);
    }
}
