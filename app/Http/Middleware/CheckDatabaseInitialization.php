<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Exception;

/**
 * Checks whether the database has been initialized.
 * Class CheckDatabaseInitialization
 * @package App\Http\Middleware
 */
class CheckDatabaseInitialization extends Middleware {

    /**
     * Handle the incoming request.
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws Exception
     */
    public function handle($request, Closure $next) {
        // Check if database has been initialized.
        if (!Schema::hasTable('sessions')) {
            throw new Exception('Die Datenbank ist noch nicht initialisiert worden. Verwenden Sie einen Blueprint mit „php artisan app:blueprint_run develop“ oder führen Sie alle Migrationen mit „php artisan migrate:fresh“ aus.');
        }

        return $next($request);
    }
}
