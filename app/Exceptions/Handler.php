<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler {

    /**
     * A list of the exception types that are not reported.
     * @var array
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     * @param Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $e) {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     * @param Request $request
     * @param Throwable $e
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $e) {
        if (is_api_request()) {
            return $this->renderApiRequest($e);
        }

        // HTTP-Methode ist nicht erlaubt.
        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        // Session Fehler, Benutzer nicht mehr eingeloggt.
        if ($e instanceof TokenMismatchException && !auth()->check()) {
            return redirect('login');
        }

        return parent::render($request, $e);
    }

    /**
     * Wandelt eine Exception in eine JSON Response um.
     * @param Throwable $exception
     * @return Response
     */
    private function renderApiRequest(Throwable $exception) {
        return match ($exception::class) {
            // Login-Fehler
            AuthenticationException::class => response()->json(['message' => __('exceptions.unauthenticated')], Response::HTTP_UNAUTHORIZED),

            // Berechtigungsfehler
            AuthorizationException::class => response()->json(['message' => __('exceptions.no_permission')], Response::HTTP_FORBIDDEN),

            // API-Key-Scope Fehler
            MissingScopeException::class => response()->json(['message' => $exception->getMessage()], Response::HTTP_FORBIDDEN),

            // HTTP-Methode ist nicht erlaubt.
            MethodNotAllowedHttpException::class => response()->json(['message' => $exception->getMessage()], Response::HTTP_METHOD_NOT_ALLOWED),

            // Model existiert nicht
            ModelNotFoundException::class, NotFoundHttpException::class => response()->json(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND),

            // Eingabe-Validierungsfehler
            ValidationException::class => response()->json([
                'message' => __('exceptions.invalid_input_data'),
                'errors' => $exception->validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY),

            // Bei DEBUG = TRUE die Fehler-Nachricht zurückgeben, andernfalls eine standardisierte Nachricht.
            default => response()->json(['message' => config('app.debug') ? $exception->getMessage() : __('exceptions.default_message')], Response::HTTP_INTERNAL_SERVER_ERROR)
        };
    }
}
