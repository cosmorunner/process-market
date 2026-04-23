<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ändert in der "processes" Tabelle die Spalte "latest_graph_id" zu "latest_version_id" und
 * "latest_published_graph_id" zu "latest_published_version_id".
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // E-Mail - Einfache Nachricht
        $template1 = \App\Models\Template::find('fa40ba9a-0f0d-4a04-a6df-2ad5ab563293');

        // Einfache HTML-Email mit einem Action-Button.
        $template2 = \App\Models\Template::find('e25edf1e-ce48-4a9d-87f6-5c58d14db22a');

        $template1->update(['mapping->message' => [
            'type' => 'string',
            'description' => 'Die Nachricht der E-Mail.',
            'value' => '[[action.outputs.message((Aktions-Daten - message))]]'
        ]]);
        $template2->update(['mapping->message' => [
            'type' => 'string',
            'description' => 'Die Nachricht der E-Mail.',
            'value' => '[[action.outputs.message((Aktions-Daten - message))]]'
        ]]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        // E-Mail - Einfache Nachricht
        $template1 = \App\Models\Template::find('fa40ba9a-0f0d-4a04-a6df-2ad5ab563293');

        // Einfache HTML-Email mit einem Action-Button.
        $template2 = \App\Models\Template::find('e25edf1e-ce48-4a9d-87f6-5c58d14db22a');

        $template1->update(['mapping->message' => [
            'type' => 'string',
            'description' => 'Die Nachricht der E-Mail.',
            'value' => '[[action.outputs.message]]'
        ]]);
        $template2->update(['mapping->message' => [
            'type' => 'string',
            'description' => 'Die Nachricht der E-Mail.',
            'value' => '[[action.outputs.message]]'
        ]]);
    }
};
