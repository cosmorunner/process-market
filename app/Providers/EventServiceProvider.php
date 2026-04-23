<?php

namespace App\Providers;

use App\Models\Access;
use App\Models\Demo;
use App\Models\License;
use App\Models\Process;
use App\Models\ProcessVersion;
use App\Models\Simulation;
use App\Models\Solution;
use App\Models\System;
use App\Models\User;
use App\Observers\AccessObserver;
use App\Observers\DemoObserver;
use App\Observers\LicenseObserver;
use App\Observers\ProcessObserver;
use App\Observers\ProcessVersionObserver;
use App\Observers\SimulationObserver;
use App\Observers\SolutionObserver;
use App\Observers\SystemObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider {

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => []
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot() {
        Access::observe(AccessObserver::class);
        Process::observe(ProcessObserver::class);
        Solution::observe(SolutionObserver::class);
        ProcessVersion::observe(ProcessVersionObserver::class);
        Simulation::observe(SimulationObserver::class);
        Demo::observe(DemoObserver::class);
        System::observe(SystemObserver::class);
        User::observe(UserObserver::class);
        License::observe(LicenseObserver::class);
    }
}
