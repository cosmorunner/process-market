<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LoginController
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     * @var string
     */
    protected $redirectTo = '/manage/profile/processes';

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Überschrieben aus dem AuthenticatesUsers-Trait
     * Zusätzlich prüfen ob der Benutzer bereits verifiziert ist.
     * Leiten Sie den Benutzer um, nachdem Sie festgestellt haben, dass er ausgesperrt ist.
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function login(Request $request) {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // Prüfen ob der Benutzer bereits verifiziert ist.
        $email = $request->get($this->username());
        $user = User::whereEmail($email)->first();

        if ($user instanceof User && !$user->hasVerifiedEmail()) {
            return redirect(route('login'))->with('warning', 'Bitte bestätigen Sie zunächst ihre Registrierung. Wir haben ihnen dazu eine E-Mail gesendet.');
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Redirects user after login
     * @return bool
     */
    public function redirectTo() {
        $context = auth()->user()->contextModel;

        return $context instanceof Organisation ? $context->profileProcessesPath() : auth()->user()->profileProcessesPath();
    }
}
