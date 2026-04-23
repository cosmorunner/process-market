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
        $template = Template::whereName('Eigene Logik - Smart-Status')->first();
        $template?->update([
            'name' => 'Eigene Logik - Smart-Status',
            'data' => 'eyMgRG9rdW1lbnRhdGlvbjogaHR0cHM6Ly90d2lnLnN5bWZvbnkuY29tL2RvYy8zLngvdGVtcGxhdGVzLmh0bWwgI30KCnsjIEhpZXIgd2lyZCBiZWlzcGllbGhhZnQgZWluZSB6dWbDpGxsaWdlIFphaGwgendpc2NoZW4gMC0xMDAgZ2V3w6RobHQuICN9CnslIHNldCB2YWx1ZSA9IHJhbmRvbSgwLCAxMDApICV9Cgp7IyBEaWVzIGlzdCBkZXIgQXVzZ2FiZS1CZXJlaWNoIGbDvHIgZGVuIFNtYXJ0LVN0YXR1cyBXZXJ0LiBFcyBtdXNzIGVpbiBnw7xsdGlnZXIgWnVzdGFuZHMtV2VydCB6dXLDvGNrZ2VnZWJlbiB3ZXJkZW4uICN9CjxvdXRwdXRzPgogICAgPHNpdHVhdGlvbj4KICAgICAgICA8c3RhdHVzIHJlZmVyZW5jZT0iU1RBVFVTX1JFRkVSRU5DRV9IRVJFIj57eyB2YWx1ZSB9fTwvc3RhdHVzPgogICAgPC9zaXR1YXRpb24+Cjwvb3V0cHV0cz4='
        ]);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        // Custom logic
        $template = Template::whereName('Eigene Logik - Aktions-Prozessor')->first();
        $template?->update([
            'name' => 'Eigene Logik - Smart-Status',
            'data' => 'eyMgRG9rdW1lbnRhdGlvbjogaHR0cHM6Ly90d2lnLnN5bWZvbnkuY29tL2RvYy8zLngvdGVtcGxhdGVzLmh0bWwgI30KCnsjIEhpZXIgd2lyZCBiZWlzcGllbGhhZnQgZWluZSB6dWbDpGxsaWdlIFphaGwgendpc2NoZW4gMC0xMDAgZ2V3w6RobHQuICN9CnslIHNldCByYW5kb21fbnVtYmVyID0gcmFuZG9tKDAsIDEwMCkgJX0KCnsjIERpZXMgaXN0IGRlciBBdXNnYWJlLUJlcmVpY2ggZsO8ciBkZW4gU21hcnQtU3RhdHVzIFdlcnQuIEVzIG11c3MgZWluIGfDvGx0aWdlciBadXN0YW5kcy1XZXJ0IHp1csO8Y2tnZWdlYmVuIHdlcmRlbi4gI30KPG91dHB1dHM+CiAgICA8c2l0dWF0aW9uPgogICAgICAgIDxzdGF0dXMgcmVmZXJlbmNlPSJTVEFUVVNfUkVGRVJFTkNFX09SX0lEX0hFUkUiPnt7IHJhbmRvbV9udW1iZXIgfX08L3N0YXR1cz4KICAgIDwvc2l0dWF0aW9uPgo8L291dHB1dHM+'
        ]);
    }
};
