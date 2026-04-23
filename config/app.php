<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Prozessfabrik'),

    /*
    |--------------------------------------------------------------------------
    | Application Description
    |--------------------------------------------------------------------------
    |
    | This value is the description of your application. This value is used when the
    | framework needs to place the application's description in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'description' => env('APP_DESCRIPTION', 'Digitalisieren Sie Prozesse für die Allisa Plattform und teilen Sie diese mit anderen Personen & Organisationen.'),

    /*
    |--------------------------------------------------------------------------
    | Application Image
    |--------------------------------------------------------------------------
    |
    | Logo der Anwendung. Wird auf der Login-Seite und in Standard-Emails/Dokumenten genutzt.
    | Relativer Pfad zu "public", z.B. "img/allisa_logo.png".
    |
    */

    'logo' => env('APP_LOGO', 'img/logo.png'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Europe/Berlin',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'de',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'de_DE',

    /*
    |--------------------------------------------------------------------------
    | Plugin Namespace
    |--------------------------------------------------------------------------
    |
    | namespace: Directories of all namespaces with the plugins, both Allisa (internal) and customer plugins (external).
    | enabled: Array of enabled external plugins e.g. namespace1/identifier1,namespace1/identifier2
    */

    'plugins' => [
        'internal_namespace' => 'allisa',
        'namespace' => [
            'internal' => env('INTERNAL_PLUGINS_NAMESPACE', 'Resources\Plugins'),
            'external' => env('EXTERNAL_PLUGINS_NAMESPACE', 'Storage\App\Plugins'),
        ],
        'enabled' => [
            'external' => array_unique(array_map('trim', explode(',', env('PLUGINS_ENABLED_EXTERNAL', ''))))
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Prozess-Export Directory
    |--------------------------------------------------------------------------
    |
    | In dieses Verzeichnis, relativ zu storage/app, werden exportierte Prozesse abgelegt.
    | Ohne "/" am Anfang und Ende.
    */

    'process_types_dir' => env('PROCESS_TYPES_DIR', 'process_types'),

    /*
    |--------------------------------------------------------------------------
    | Prozesstyp-Commands Namespace
    |--------------------------------------------------------------------------
    |
    | Namespace hinter dem alle Prozesstyp-Commands liegen.
    */

    'process_type_commands_namespace' => env('PROCESS_TYPE_COMMANDS_NAMESPACE', 'App\\ProcessType\\Commands\\'),

    /*
    |--------------------------------------------------------------------------
    | Environment-Blueprint-Commands Namespace
    |--------------------------------------------------------------------------
    |
    | Namespace hinter dem alle Environment-Blueprint-Commands liegen.
    */

    'environment_blueprint_commands_namespace' => env('ENVIRONMENT_BLUEPRINT_COMMANDS_NAMESPACE', 'App\\Environment\\Commands\\'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Redis
    |--------------------------------------------------------------------------
    |
    | Flagge ob der Redis-Cache geladen werden soll.
    | Bei FALSE wird der Cache geflusht in der boot() Methode im AppServiceProvider.
    */

    'redis_enabled' => env('REDIS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Process version history length
    |--------------------------------------------------------------------------
    |
    | Determines the length of the history each process version can have.
    | History items are deleted once the length has been exceeded.
    */

    'process_version_history_length' => env('PROCESS_VERSION_HISTORY_LENGTH', 10),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Guzzle HTTP-Requests
    |--------------------------------------------------------------------------
    | Options for the guzzle http requests made by the connectors.
    */
    'guzzle' => [
        'verify' => env('GUZZLE_VERIFY', true),
        'connect_timeout' => env('GUZZLE_CONNECT_TIMEOUT', 5),
        'timeout' => env('GUZZLE_TIMEOUT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Home Configuration
    |--------------------------------------------------------------------------
    | Options for the landing page. "explore_processes" references the
    | ExploreProcesses component.
    */
    'home' => [
        'explore_processes' => [
            'tags' => env('HOME_EXPLORE_PROCESSES_TAGS', 'Smart University|smart-university,Smart Organisation|smart-organisation,Smart Factory|smart-factory'),
            'selected_tags' => env('HOME_EXPLORE_PROCESSES_SELECTED_TAGS', ''),
            'count_per_page' => env('HOME_EXPLORE_PROCESSES_COUNT_PER_PAGE', 6),
        ]
    ],

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\TelescopeServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\ViewServiceProvider::class,
        App\Providers\HorizonServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Purify' => \Stevebauman\Purify\Facades\Purify::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Image' => Intervention\Image\Facades\Image::class
    ],

];
