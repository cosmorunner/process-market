<?php

use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void {

        Organisation::all()->each(function ($organisation) {
            $role = Role::factory()->ofOwnerType()->create();
            $organisation->addRole($role);
        });
    }

    public function down(): void {
        Organisation::all()->each(function ($organisation) {
            $organisation->removeRole($organisation->adminRole());
        });
    }
};
