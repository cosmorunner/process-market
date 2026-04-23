<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTemplatesTable
 */
return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::table('templates')->insert(
            [
                'id' => 'fa40ba9a-0f0d-4a04-a6df-2ad5ab563293',
                'name' => 'E-Mail - Einfache Nachricht',
                'description' => 'HTML-Email mit Logo und Fußzeile.',
                'data' => 'PGRpdiBjbGFzcz0iY29udGFpbmVyIHB5LTUgYmctbGlnaHQiPgogICAgPGRpdiBjbGFzcz0icm93IG1iLTUganVzdGlmeS1jb250ZW50LWNlbnRlciI+CiAgICAgICAgPGRpdiBjbGFzcz0iY29sLTggYWxpZ24taXRlbXMtY2VudGVyIGQtZmxleCI+CiAgICAgICAgICAgIDxpbWcgY2xhc3M9ImJvcmRlci0wIiBhbHQ9IiIgc3JjPSJ7eyBhcHBfaW1hZ2UgfX0iIHdpZHRoPSJhdXRvIiBoZWlnaHQ9IjUwIj4KICAgICAgICAgICAgPGgyIGNsYXNzPSJtbC0yIGQtaW5saW5lLWJsb2NrIG1iLTAiPnt7IGFwcF9uYW1lIH19PC9oMj4KICAgICAgICA8L2Rpdj4KICAgIDwvZGl2PgogICAgPGRpdiBjbGFzcz0icm93IG1iLTUganVzdGlmeS1jb250ZW50LWNlbnRlciI+CiAgICAgICAgPGRpdiBjbGFzcz0iY29sLTggYmctd2hpdGUgcHktMiBweC0zIGJvcmRlciBhbGlnbi1pdGVtcy1jZW50ZXIiPgogICAgICAgICAgICA8ZGl2IGNsYXNzPSJyb3ciPgogICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iY29sIj4KICAgICAgICAgICAgICAgICAgICA8Yj5IYWxsbyE8L2I+CiAgICAgICAgICAgICAgICAgICAgPHA+e3sgbWVzc2FnZSB9fTwvcD4KICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICA8L2Rpdj4KICAgICAgICA8L2Rpdj4KICAgIDwvZGl2PgogICAgPGRpdiBjbGFzcz0icm93Ij4KICAgICAgICA8ZGl2IGNsYXNzPSJjb2wgdGV4dC1jZW50ZXIiPgogICAgICAgICAgICA8c21hbGwgY2xhc3M9InRleHQtbXV0ZWQiPlBvd2VyZWQgYnkgPGEgY2xhc3M9InRleHQtZGFyayIgaHJlZj0iaHR0cHM6Ly9hbGxpc2Euc29mdHdhcmUvIj5BbGxpc2EgU29mdHdhcmUgR21iSDwvYT48L3NtYWxsPgogICAgICAgIDwvZGl2PgogICAgPC9kaXY+CjwvZGl2Pgo=',
                'file' => 'email-simple.twig',
                'preview' => 'img/email-simple.png',
                'type' => 'html',
                'mapping' => json_encode([
                    'message' => [
                        'type' => 'string',
                        'description' => 'Die Nachricht der E-Mail.',
                        'value' => '[[action.outputs.message]]'
                    ]
                ])
            ]
        );

        DB::table('templates')->insert(
            [
                'id' => 'e25edf1e-ce48-4a9d-87f6-5c58d14db22a',
                'name' => 'E-Mail - Nachricht mit Button',
                'description' => 'Einfache HTML-Email mit einem Action-Button.',
                'data' => 'PGRpdiBjbGFzcz0iY29udGFpbmVyIHB5LTUgYmctbGlnaHQiPgogICAgPGRpdiBjbGFzcz0icm93IG1iLTUganVzdGlmeS1jb250ZW50LWNlbnRlciI+CiAgICAgICAgPGRpdiBjbGFzcz0iY29sLTggYWxpZ24taXRlbXMtY2VudGVyIGQtZmxleCI+CiAgICAgICAgICAgIDxpbWcgY2xhc3M9ImJvcmRlci0wIiBhbHQ9IiIgc3JjPSJ7eyBhcHBfaW1hZ2UgfX0iIGhlaWdodD0iNTAiPgogICAgICAgICAgICA8aDIgY2xhc3M9Im1sLTIgZC1pbmxpbmUtYmxvY2sgbWItMCI+e3sgYXBwX25hbWUgfX08L2gyPgogICAgICAgIDwvZGl2PgogICAgPC9kaXY+CiAgICA8ZGl2IGNsYXNzPSJyb3cgbWItNSBqdXN0aWZ5LWNvbnRlbnQtY2VudGVyIj4KICAgICAgICA8ZGl2IGNsYXNzPSJjb2wtOCBiZy13aGl0ZSBwLTMgYm9yZGVyIGFsaWduLWl0ZW1zLWNlbnRlciI+CiAgICAgICAgICAgIDxkaXYgY2xhc3M9InJvdyI+CiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJjb2wiPgogICAgICAgICAgICAgICAgICAgIDxiPkhhbGxvITwvYj4KICAgICAgICAgICAgICAgICAgICA8cD57eyBtZXNzYWdlIH19PC9wPgogICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICA8ZGl2IGNsYXNzPSJyb3ciPgogICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iY29sIj4KICAgICAgICAgICAgICAgICAgICA8YSBocmVmPSJ7eyB1cmwgfX0iIGNsYXNzPSJidG4gYnRuLXByaW1hcnkiIHRhcmdldD0iX2JsYW5rIiByZWw9Im5vb3BlbmVyIj57eyBidXR0b25fbGFiZWwgfX08L2E+CiAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgICAgIDxocj4KICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgIDxocj4KICAgICAgICAgICAgPGRpdiBjbGFzcz0icm93Ij4KICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImNvbCI+CiAgICAgICAgICAgICAgICAgICAgPHNtYWxsIGNsYXNzPSJ0ZXh0LW11dGVkIj4KICAgICAgICAgICAgICAgICAgICAgICAgPHNwYW4+V2VubiBTaWUgUHJvYmxlbWUgaGFiZW4gYXVmIGRpZSBTY2hhbHRmbMOkY2hlIHp1IGtsaWNrZW4sIGtvcGllcmVuIFNpZSBkaWUgZm9sZ2VuZGUgVVJMIHVuZCBmw7xnZW4gU2llIHNpZSBpbiBJaHJlbiBXZWJicm93c2VyIGVpbjo8L3NwYW4+CiAgICAgICAgICAgICAgICAgICAgICAgIDxhIGhyZWY9Int7IHVybCB9fSI+e3sgdXJsIH19PC9hPgogICAgICAgICAgICAgICAgICAgIDwvc21hbGw+CiAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgPC9kaXY+CiAgICA8L2Rpdj4KICAgIDxkaXYgY2xhc3M9InJvdyI+CiAgICAgICAgPGRpdiBjbGFzcz0iY29sIHRleHQtY2VudGVyIj4KICAgICAgICAgICAgPHNtYWxsIGNsYXNzPSJ0ZXh0LW11dGVkIj5Qb3dlcmVkIGJ5IDxhIGNsYXNzPSJ0ZXh0LWRhcmsiIGhyZWY9Imh0dHBzOi8vYWxsaXNhLnNvZnR3YXJlLyI+QWxsaXNhIFNvZnR3YXJlIEdtYkg8L2E+PC9zbWFsbD4KICAgICAgICA8L2Rpdj4KICAgIDwvZGl2Pgo8L2Rpdj4K',
                'file' => 'email-simple-with-button.twig',
                'preview' => 'img/email-simple-with-button.png',
                'type' => 'html',
                'mapping' => json_encode([
                    'message' => [
                        'type' => 'string',
                        'description' => 'Die Nachricht der E-Mail.',
                        'value' => '[[action.outputs.message]]'
                    ],
                    'url' => [
                        'type' => 'string',
                        'description' => 'Die URL für den Action-Button.',
                        'value' => 'https://example.com'
                    ],
                    'button_label' => [
                        'type' => 'string',
                        'description' => 'Das Label des Action-Buttons.',
                        'value' => 'Hier klicken'
                    ]
                ])
            ]
        );

        DB::table('templates')->insert(
            [
                'id' => 'd98b04c3-be09-47c0-866d-e3746463f745',
                'name' => 'Leere Vorlage',
                'description' => 'Individuelle Gestaltung der Vorlage.',
                'data' => 'PGRpdiBjbGFzcz0iY29udGFpbmVyIj4KICAgIDxkaXYgY2xhc3M9InJvdyI+CiAgICAgICAgPGRpdiBjbGFzcz0iY29sIj4KICAgICAgICAgICAgPHA+SGFsbG8hPC9wPgogICAgICAgIDwvZGl2PgogICAgPC9kaXY+CjwvZGl2Pg==',
                'file' => 'empty-template.twig',
                'preview' => 'img/empty-template.png',
                'type' => 'html',
                'mapping' => json_encode([])
            ]
        );

        DB::table('templates')->insert(
            [
                'id' => 'aa968990-5317-425d-b841-703f7397d945',
                'name' => 'Eigene Logik',
                'description' => 'Vorlage für den Eigene-Logik Aktions-Prozessor.',
                'data' => 'eyMgRG9rdW1lbnRhdGlvbjogaHR0cHM6Ly90d2lnLnN5bWZvbnkuY29tL2RvYy8zLngvdGVtcGxhdGVzLmh0bWwgI30KCnsjCkJlcmVjaG5lIGJhc2llcmVuZCBhdWYgZWlnZW5lciBMb2dpayBiZXN0aW1tdGUgQWt0aW9ucy1EYXRlbi4gU2V0emUgZGllIGJlcmVjaG5ldGVuIFZhcmlhYmxlbgphdWYgZWluZW4gIjxvdXRwdXQ+Ii1UYWcgaW0gIjxhY3Rpb24+Ii1UYWcuCiN9Cgp7JSBzZXQgZm9vID0gJ2ZvbycgJX0KeyUgc2V0IGJhciA9ICdiYXInICV9Cgp7IyBEaWVzIGlzdCBkZXIgQXVzZ2FiZS1CZXJlaWNoLiBEaWUgVGFnLVN0cnVrdHVyIG11c3MgYmVpYmVoYWx0ZW4gd2VyZGVuLiAjfQo8b3V0cHV0cz4KICAgIDxhY3Rpb24+CiAgICAgICAgPG91dHB1dCBuYW1lPSJkYXRhXzEiPnt7IGZvbyB9fTwvb3V0cHV0PgogICAgICAgIDxvdXRwdXQgbmFtZT0iZGF0YV8yIj57eyBiYXIgfX08L291dHB1dD4KICAgIDwvYWN0aW9uPgo8L291dHB1dHM+',
                'file' => 'empty-custom-logik.twig',
                'preview' => 'img/empty-template.png',
                'type' => 'custom_logic',
                'mapping' => json_encode([])
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::table('templates')->truncate();
    }
};
