<?php /** @noinspection PhpUndefinedNamespaceInspection */

use App\ActionRule;
use App\Email;
use App\MenuItem;
use App\Output;
use App\Processors\GenerateDocument;
use App\Processors\SendEmail;
use App\Processors\SendPushMessage;
use App\Processors\UploadFile;
use App\StatusRule;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Initiale System-Konfigruation für Neuinstallationen
|--------------------------------------------------------------------------
|
| Die untenstehende Konfiguration repräsentieren den initialen Systemzustand.
| Sie wird bei Neuinstallationen via dem 'DatabaseSeeder' geladen.
|
*/

return [

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
            'first_name' => 'Robert',
            'last_name' => 'Howdi',
            'email' => 'robert@example.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            // password
            'remember_token' => Str::random(10),
        ],
    ],
];
