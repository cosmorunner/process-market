<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Organisation;

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
        DB::table('permissions')->truncate();
        DB::table('permission_role')->truncate();

        // Neue Rechte hinzufügen
        Permission::create([
            'name' => 'Prozesse & Lösungen testen',
            'description' => 'Prozesse & Lösungen testen',
            'ident' => 'demos.view',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Prozesse erstellen',
            'description' => 'Prozesse erstellen',
            'ident' => 'processes.create',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Prozesse entwickeln',
            'description' => 'Prozesse entwickeln',
            'ident' => 'processes.update',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Prozesse einsehen',
            'description' => 'Prozesse einsehen',
            'ident' => 'processes.view',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Prozesse löschen / archivieren',
            'description' => 'Prozesse löschen / archivieren',
            'ident' => 'processes.delete',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Prozess-Versionen fertigstellen',
            'description' => 'Prozess-Versionen fertigstellen',
            'ident' => 'process_versions.create',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Prozess-Versionen exportieren',
            'description' => 'Prozess-Versionen exportieren',
            'ident' => 'process_versions.export',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Lösungen erstellen',
            'description' => 'Lösungen erstellen',
            'ident' => 'solutions.create',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Lösungen entwickeln',
            'description' => 'Lösungen entwickeln',
            'ident' => 'solutions.update',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Lösungen einsehen',
            'description' => 'Lösungen einsehen',
            'ident' => 'solutions.view',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Lösungen löschen / archivieren',
            'description' => 'Lösungen löschen / archivieren',
            'ident' => 'solutions.delete',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Lösungs-Versionen fertigstellen',
            'description' => 'Lösungs-Versionen fertigstellen',
            'ident' => 'solution_versions.create',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Lösungs-Versionen exportieren',
            'description' => 'Lösungs-Versionen exportieren',
            'ident' => 'solution_versions.export',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Mitglieder verwalten',
            'description' => 'Mitglieder verwalten',
            'ident' => 'members.manage',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Mitglieder einsehen',
            'description' => 'Mitglieder einsehen',
            'ident' => 'members.view',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Lizenzen einsehen',
            'description' => 'Lizenzen einsehen',
            'ident' => 'licenses.view',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Lizenzen verwalten',
            'description' => 'Lizenzen verwalten',
            'ident' => 'licenses.manage',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Allisa Plattformen verwalten',
            'description' => 'Allisa Plattformen verwalten',
            'ident' => 'platforms.manage',
            'active' => true
        ]);

        Permission::create([
            'name' => 'Organisation verwalten',
            'description' => 'Organisation verwalten',
            'ident' => 'organisation.manage',
            'active' => true
        ]);

        // Neue Rollen hinzufügen
        /* @var Organisation $organisation */
        foreach (Organisation::all() as $organisation) {
            $organisation->roles()->createMany([
                [
                    'name' => 'Manager',
                    'description' => 'Prozesse, Lösungen, Plattformen und Lizenzen verwalten.',
                    'is_admin' => false
                ],
                [
                    'name' => 'Reporter',
                    'description' => 'Prozesse & Lösungen einsehen und testen.',
                    'is_admin' => false
                ]
            ]);
        }

        // Rechte zu den Rollen zuordnen.
        foreach (Role::all() as $role) {
            /* @var Role $role */
            if ($role->name === 'Prozess-Entwickler') {
                $role->update(['description' => 'Prozesse & Lösungen entwickeln & exportieren.']);
            }

            switch ($role->name) {
                case 'Administrator':
                    $permissions = Permission::whereIn('ident', config('roles.admin.permissions', []))->get();
                    $role->permissions()->saveMany($permissions);

                    break;
                case 'Manager':
                    $permissions = Permission::whereIn('ident', config('roles.manager.permissions', []))->get();
                    $role->permissions()->saveMany($permissions);

                    break;
                case 'Prozess-Entwickler':
                    $permissions = Permission::whereIn('ident', config('roles.developer.permissions', []))->get();
                    $role->permissions()->saveMany($permissions);

                    break;
                case 'Reporter':
                    $permissions = Permission::whereIn('ident', config('roles.reporter.permissions', []))->get();
                    $role->permissions()->saveMany($permissions);

                    break;
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::table('permissions')->truncate();
        DB::table('permission_role')->truncate();
        Role::whereIn('name', ['Manager', 'Reporter'])->delete();
    }
};
