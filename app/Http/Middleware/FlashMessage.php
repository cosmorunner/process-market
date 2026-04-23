<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Prüft ob die öffentlichen Routen der Public-Apis erreichbar sind. Flagge wird in der .env-Datei
 * unter "PUBLIC_API_ENABLED" gesetzt.
 * Class Localization
 * @package App\Http\Middleware
 */
class FlashMessage {

    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($request->query->has('fm') && auth()->check()) {
            // Flash-Message Nachricht
            $flashMessage = $request->query->get('fm');

            // Flash-Message Typ (info, error,...)
            $type = $request->query->get('fmt', 'success');

            // Es wird davon ausgegangen, dass es ein Base64 encoded String ist. Dieser wird auf Gültigkeit überprüft.
            if (base64_encode(base64_decode($flashMessage, true)) === $flashMessage) {
                Session::now($type, base64_decode($flashMessage));
            }

            return $next($request);
        }

        return $next($request);
    }
}
