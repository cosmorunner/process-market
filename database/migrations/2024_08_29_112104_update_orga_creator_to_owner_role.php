<?php

use App\Models\Organisation;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {

    public function up(): void {
        Organisation::all()->each(function (Organisation $organisation) {
            $organisationCreator = $organisation->accesses()->oldest()->first()?->recipient;

            if ($organisationCreator) {
                $organisation->updateAccess($organisationCreator, $organisation->ownerRole());
            }
        });
    }

    public function down(): void {
        Organisation::all()->each(function (Organisation $organisation) {
            $ownerMember = $organisation->ownerMember();


            if ($ownerMember) {
                $organisation->updateAccess($ownerMember, $organisation->adminRole());
            }
        });
    }
};
