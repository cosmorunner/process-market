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
        Template::create([
            'name' => 'Eigene Logik - Smart-Status',
            'description' => 'Vorlage für den Smart-Status vom Typ "Eigene Logik".',
            'data' => 'eyMgRG9rdW1lbnRhdGlvbjogaHR0cHM6Ly90d2lnLnN5bWZvbnkuY29tL2RvYy8zLngvdGVtcGxhdGVzLmh0bWwgI30KCnsjIEhpZXIgd2lyZCBiZWlzcGllbGhhZnQgZWluZSB6dWbDpGxsaWdlIFphaGwgendpc2NoZW4gMC0xMDAgZ2V3w6RobHQuICN9CnslIHNldCByYW5kb21fbnVtYmVyID0gcmFuZG9tKDAsIDEwMCkgJX0KCnsjIERpZXMgaXN0IGRlciBBdXNnYWJlLUJlcmVpY2ggZsO8ciBkZW4gU21hcnQtU3RhdHVzIFdlcnQuIEVzIG11c3MgZWluIGfDvGx0aWdlciBadXN0YW5kcy1XZXJ0IHp1csO8Y2tnZWdlYmVuIHdlcmRlbi4gI30KPG91dHB1dHM+CiAgICA8c2l0dWF0aW9uPgogICAgICAgIDxzdGF0dXMgcmVmZXJlbmNlPSJTVEFUVVNfUkVGRVJFTkNFX09SX0lEX0hFUkUiPnt7IHJhbmRvbV9udW1iZXIgfX08L3N0YXR1cz4KICAgIDwvc2l0dWF0aW9uPgo8L291dHB1dHM+',
            'preview' => 'img/empty-template.png',
            'type' => 'custom_logic',
            'file' => 'empty-custom-logic.twig',
            'mapping' => []
        ]);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Template::whereName('Eigene Logik - Smart-Status')->delete();
    }
};
