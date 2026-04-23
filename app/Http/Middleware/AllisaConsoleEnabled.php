<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class AllisaConsoleEnabled
 * @package App\Http\Middleware
 */
class AllisaConsoleEnabled {
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        if (!config('allisa.console.enabled')) {
            abort(404);
        }

        return $next($request);
    }
}
