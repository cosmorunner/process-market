<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Template;

/**
 * Updates the custom logic template
 */
return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        // Custom logic
        $template = Template::whereName('Eigene Logik')->first();
        $template?->update([
            'data' => ['eyMgRG9rdW1lbnRhdGlvbjogaHR0cHM6Ly90d2lnLnN5bWZvbnkuY29tL2RvYy8zLngvdGVtcGxhdGVzLmh0bWwgI30KCnslIHNldCBmb28gPSAnZm9vJyAlfQp7JSBzZXQgYmFyID0gJ2JhcicgJX0KCnsjIERpZXMgaXN0IGRlciBBdXNnYWJlLUJlcmVpY2ggZsO8ciBkaWUgQWt0aW9ucy1EYXRlbi4gI30KPG91dHB1dHM+CiAgICA8YWN0aW9uPgogICAgICAgIDxvdXRwdXQgbmFtZT0iZGF0YV8xIj57eyBmb28gfX08L291dHB1dD4KICAgICAgICA8b3V0cHV0IG5hbWU9ImRhdGFfMiI+e3sgYmFyIH19PC9vdXRwdXQ+CiAgICA8L2FjdGlvbj4KPC9vdXRwdXRzPg==']
        ]);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {}
};
