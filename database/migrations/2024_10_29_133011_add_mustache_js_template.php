<?php

use App\Models\Template;
use Illuminate\Database\Migrations\Migration;

/**
 * Adds Mustache JS template.
 */
return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        DB::table('templates')->insert([
            'id' => '5c7b0b68-42ed-4a02-b498-f4c5162b8c25',
            'name' => 'Eigenes HTML - Listenspalte',
            'description' => 'Mustache.js Vorlage, um eigenes HTML in einer Listenspalte anzuzeigen.',
            'data' => 'e3shIERpZXMgaXN0IGVpbmUgTXVzdGFjaGUuanMgVm9ybGFnZS4gRG9rdW1lbnRhdGlvbjogaHR0cHM6Ly9naXRodWIuY29tL2phbmwvbXVzdGFjaGUuanMgfX0Ke3shIEhpbndlaXM6IEVzIGvDtm5uZW4gZGllIEJvb3RzdHJhcCA0LjYgQ1NTLUtsYXNzZW4gZ2VudXR6dCB3ZXJkZW4uIH19Cgp7e2NvbW1lbnR9fQoKPHAgY2xhc3M9Im1iLTAiPkFydGlrZWw6PC9wPgoKPHVsPgogICAge3sjaXRlbXN9fQogICAgPGxpPgogICAgICAgIDxzcGFuIGNsYXNzPSJ0ZXh0LXByaW1hcnkiPnt7YXJ0aWNsZX19PC9zcGFuPgogICAgICAgIDxzcGFuPiAtIDwvc3Bhbj4KICAgICAgICA8c3BhbiBjbGFzcz0idGV4dC1kYW5nZXIiPnt7cHJpY2V9fTwvc3Bhbj4KICAgIDwvbGk+CiAgICB7ey9pdGVtc319CjwvdWw+Cgp7eyNtdXN0QmVBcHByb3ZlZH19CiAgICA8cCBjbGFzcz0ibWItMCI+RGllIEJlc3RlbGx1bmcgbXVzcyBkdXJjaCBmb2xnZW5kZSBQZXJzb25lbiBmcmVpZ2VnZWJlbiB3ZXJkZW46PC9wPgogICAge3sjYXBwcm92ZXJzfX0KICAgICAgICA8c3BhbiBjbGFzcz0ibXItMSBmb250LXdlaWdodC1ib2xkIj57ey59fTwvc3Bhbj4KICAgIHt7L2FwcHJvdmVyc319Cnt7L211c3RCZUFwcHJvdmVkfX0=',
            'file' => 'mustache-list-column-articles.txt',
            'preview' => 'img/mustache-simple.png',
            'type' => 'mustache_list_column',
            'mapping' => json_encode([
                'js' => [
                    'type' => 'js',
                    'description' => '',
                    'value' => '// JavaScript
// Die Variable "row" ist ein Objekt mit den Werten der Listenzeile. 
// Siehe "Vorschau -> Werte" für verfügbare Werte der Prozesslisten.
// Zeilenwert mit "row.list_alias" auslesen.

// Beispiel
let mustBeApproved = true;
let approvers = ["Bob", "Anna", "Karl"];
let comment = "Bitte folgende Artikel bestellen."

let items = [
    {
        "article": "Laptop",
        "price": 1500
    },
    {
        "article": "Monitor",
        "price": 500
    }
]

// Es muss ein Objekt zurückgeben werden. Auf die Objekt-Schlüssel kann in der Mustache.js Vorlage zugegriffen werden.
return {
    "mustBeApproved": mustBeApproved,
    "hasApprovers": approvers.length,
    "approvers": approvers,
    "comment": comment,
    "items": items,
    "total": items.map(item => item.price).reduce((a, b) => a + b, 0),
}'
                ]
            ])
        ]);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Template::whereName('Eigenes HTML - Listenspalte')->delete();
    }
};