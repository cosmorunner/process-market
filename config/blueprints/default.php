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
    'name' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Benutzer
    |--------------------------------------------------------------------------
    |
    | Die untenstehende Konfiguration repräsentieren die Standard-Benutzer des Systems.
    | Sie wird bei Neuinstallationen genutzt via dem "UsersTableSeeder" geladen.
    |
    */
    'users' => []
];
