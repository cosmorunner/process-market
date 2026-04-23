<?php

return [
    'cloud_host' => env('ALLISA_CLOUD_HOST', ''), // z.B. http://deac30fb-ad50-4aef-a4b2-d751d1556237.vm.test,
    'oauth_endpoint' => env('ALLISA_OAUTH_ENDPOINT', ''), // http://vm.test/oauth/token
    'api_endpoint' => env('ALLISA_API_ENDPOINT', ''),
    'api_sync_route' => env('ALLISA_API_SYNC_ROUTE', ''),
    'password_client_id' => env('ALLISA_PASSWORD_CLIENT_ID', ''),
    'password_client_secret' => env('ALLISA_PASSWORD_CLIENT_SECRET', ''),
    'admin_email' => env('ALLISA_ADMIN_EMAIL', ''),
    'admin_password' => env('ALLISA_ADMIN_PASSWORD', ''),
    // Minimum version of the allisa platform where process types can be exported to.
    'min_version' => env('ALLISA_MIN_VERSION', '1.36.0'),
    'api_about_route' => env('ALLISA_API_ABOUT_ROUTE', 'api/v1/about'),

    /*
     * Daten für die Erzeugung von Simulationen
     */
    'simulation' => [
        'host' => env('ALLISA_SIMULATION_HOST', ''), // z.B. http://deac30fb-ad50-4aef-a4b2-d751d1556237.vm.test
        'api_endpoint' => env('ALLISA_SIMULATION_API_ENDPOINT', ''),
        'password_client_id' => env('ALLISA_SIMULATION_PASSWORD_CLIENT_ID', ''),
        'password_client_secret' => env('ALLISA_SIMULATION_PASSWORD_CLIENT_SECRET', ''),
        'user_id' => env('ALLISA_SIMULATION_USER_ID', ''),
        'user_identity_id' => env('ALLISA_SIMULATION_USER_IDENTITY_ID', ''),
        'user_email' => env('ALLISA_SIMULATION_USER_EMAIL', ''),
        'user_password' => env('ALLISA_SIMULATION_USER_PASSWORD', ''),
        'secret' => env('ALLISA_SIMULATION_SECRET', ''),
        'process_id' => env('ALLISA_SIMULATION_PROCESS_ID', ''),
        'default_system_role_id' => env('ALLISA_SIMULATION_DEFAULT_SYSTEM_ROLE_ID', ''),
        'default_director_role_id' => env('ALLISA_SIMULATION_DEFAULT_DIRECTOR_ROLE_ID', ''),
        'default_group_id' => env('ALLISA_SIMULATION_DEFAULT_GROUP_ID', ''),
        'permission_process_create' => env('ALLISA_SIMULATION_PERMISSION_PROCESS_CREATE', ''),
        'permission_process_delete' => env('ALLISA_SIMULATION_PERMISSION_PROCESS_DELETE', ''),
        // Sets the environment name for the Allisa Plattform Simulation Instance.
        // - uuid: The environment (.env-file and SQLite-DB-Name) is equal to the simulation id.
        // - simulation: The environment (.env-file and SQLite-DB-Name) is set to "simulation". Useful for development puporses
        // when wildcard subdomain is too complex to configure in docker environment.
        'environment_name' => env('ALLISA_SIMULATION_ENVIRONMENT_NAME', 'uuid'),
    ],

    /*
     * Daten für die Erzeugung von Live-Demos
     */
    'live_demo' => [
        'host' => env('ALLISA_LIVE_DEMO_HOST', ''), // z.B. http://deac30fb-ad50-4aef-a4b2-d751d1556237.vm.test
        'api_endpoint' => env('ALLISA_LIVE_DEMO_API_ENDPOINT', ''),
        'password_client_id' => env('ALLISA_LIVE_DEMO_PASSWORD_CLIENT_ID', ''),
        'password_client_secret' => env('ALLISA_LIVE_DEMO_PASSWORD_CLIENT_SECRET', ''),
        'user_id' => env('ALLISA_LIVE_DEMO_USER_ID', ''),
        'user_identity_id' => env('ALLISA_LIVE_DEMO_USER_IDENTITY_ID', ''),
        'user_email' => env('ALLISA_LIVE_DEMO_USER_EMAIL', ''),
        'user_password' => env('ALLISA_LIVE_DEMO_USER_PASSWORD', ''),
        'admin_id' => env('ALLISA_LIVE_DEMO_ADMIN_ID', ''),
        'admin_identity_id' => env('ALLISA_LIVE_DEMO_ADMIN_IDENTITY_ID', ''),
        'admin_email' => env('ALLISA_LIVE_DEMO_ADMIN_EMAIL', ''),
        'admin_password' => env('ALLISA_LIVE_DEMO_ADMIN_PASSWORD', ''),
        'secret' => env('ALLISA_LIVE_DEMO_SECRET', ''),
        'process_id' => env('ALLISA_LIVE_DEMO_PROCESS_ID', ''),
        'default_director_role_id' => env('ALLISA_LIVE_DEMO_DEFAULT_DIRECTOR_ROLE_ID', ''),
        'permission_process_create' => env('ALLISA_LIVE_DEMO_PERMISSION_PROCESS_CREATE', ''),
        'permission_process_delete' => env('ALLISA_LIVE_DEMO_PERMISSION_PROCESS_DELETE', '')
    ],

    'console' => [
        'api_endpoint' => env('ALLISA_CONSOLE_API_ENDPOINT', ''),
        'oauth_endpoint' => env('ALLISA_CONSOLE_OAUTH_ENDPOINT', ''),
        'client_id' => env('ALLISA_CONSOLE_CLIENT_ID', ''),
        'client_secret' => env('ALLISA_CONSOLE_CLIENT_SECRET', ''),
        'enabled' => env('ALLISA_CONSOLE_ENABLED', false)
    ]
];
