<?php

/*
|--------------------------------------------------------------------------
| Initiale System-Konfigruation für Neuinstallationen
|--------------------------------------------------------------------------
|
| Die untenstehende Konfiguration repräsentieren den initialen Systemzustand.
| Sie wird bei Neuinstallationen via dem 'DatabaseSeeder' geladen.
| WICHTIG: Es dürfen keine Klassen (z.B. für Konstanten) genutzt werden, weil die Allisa Console diese Datei einließt.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Blueprint-Name
    |--------------------------------------------------------------------------
    |
    | Wird genutzt um beim Seeden den Blueprint zu identifizieren.
    | Entspricht dem Dateinamen.
    |
    */
    'name' => 'develop',

    /*
    |--------------------------------------------------------------------------
    | Benutzer
    |--------------------------------------------------------------------------
    |
    | Die untenstehende Konfiguration repräsentieren die Standard-Benutzer des Systems.
    | Sie wird bei Neuinstallationen genutzt via dem "UsersTableSeeder" geladen.
    |
    */
    'users' => [
        [
            'id' => '7c759c38-c520-4aab-859b-9af8627a3386',
            'namespace' => 'testuser',
            'email' => 'test@example.com',
            'email_verified_at' => now()->toString(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            // password
            'remember_token' => 'dk4mghj3',
        ],
        [
            'id' => '4f312743-c926-4c77-8b93-9f77f8f01534',
            'namespace' => 'viktoria',
            'email' => 'viktoria@example.com',
            'email_verified_at' => now()->toString(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            // password
            'remember_token' => 'dk4mghj3',
        ],
        [
            'id' => 'd7b265d8-678f-410d-b2bc-5a5eddfd1068',
            'namespace' => 'florian',
            'email' => 'florian@example.com',
            'email_verified_at' => now()->toString(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            // password
            'remember_token' => 'dk4mghj3',
        ],
        [
            'id' => '9e51c087-dead-467c-8d83-66ea580ae438',
            'namespace' => 'jan',
            'email' => 'jan@example.com',
            'email_verified_at' => now()->toString(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            // password
            'remember_token' => 'dk4mghj3',
        ],
		[
			'id' => 'd7bacf5c-181b-44a9-8417-d29bd45d0053',
			'namespace' => 'marten',
			'email' => 'marten@example.com',
			'email_verified_at' => now()->toString(),
			'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
			// password
			'remember_token' => 'dk4mghj3',
		],
        [
            'id' => '74bd85cf-2a8c-4289-b80d-48c9e9089575',
            'namespace' => 'joy',
            'email' => 'joy@example.com',
            'email_verified_at' => now()->toString(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            // password
            'remember_token' => 'dk4mghj3',
        ],
    ]
];
