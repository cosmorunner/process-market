<?php

namespace App\Providers;

use App\Models\ProcessLicense;
use App\Policies\LicensePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

/**
 * Class AuthServiceProvider
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider {

    /**
     * The policy mappings for the application.
     * @var array
     */
    protected $policies = [
        // Policies werden in der Regel auto-discovered aufgrund der korrekten Namenskonvention, daher müssen die meisten
        // hier nicht eingetragen werden. Hier werden nur die abgeleiteten Klassen von Eloquent-Models gelistet
        // weil der Constructor in der Policy andernfalls die abgeleitete Klasse nicht erkennt und den Request ablehnt.
        // 'App\Model' => 'App\Policies\ModelPolicy',
        ProcessLicense::class => LicensePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     * @return void
     */
    public function boot() {
        // Name des Cookies für API-Requests aus dem nativen Web-Frontend.
        Passport::cookie('api_token');
    }

}
