<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void {
        Permission::create([
            'ident' => 'admins.manage',
            'name' => 'Administratoren verwalten',
            'description' => 'Administratoren verwalten',
            'active' => true
        ]);
    }

    public function down(): void {
        Permission::whereIdent('admins.manage')->delete();
    }
};
