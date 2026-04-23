<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Class OrganisationsTableSeeder
 * @package Database\Seeders
 */
class OrganisationsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // Test-Orga
        /* @var Organisation $organisation */
        $organisation = Organisation::create([
            'name' => 'Allisa UG',
            'namespace' => 'allisa',
            'description' => 'Industrie 4.0 Digitalisaierungsplattform.'
        ]);

        // Neue Rechte hinzufügen
        $p1 = Permission::create([
            'name' => 'Demos starten',
            'description' => 'Demos starten',
            'ident' => 'demos.view',
            'active' => true
        ]);

        $p2 = Permission::create([
            'name' => 'Prozess-Metadaten bearbeiten',
            'description' => 'Prozess-Metadaten bearbeiten',
            'ident' => 'processes.update',
            'active' => true
        ]);

        $p3 = Permission::create([
            'name' => 'Prozesse erstellen & bearbeiten',
            'description' => 'Prozesse erstellen & bearbeiten',
            'ident' => 'processes.create',
            'active' => true
        ]);

        $p4 = Permission::create([
            'name' => 'Prozesse einsehen',
            'description' => 'Prozesse einsehen',
            'ident' => 'processes.view',
            'active' => true
        ]);

        $p5 = Permission::create([
            'name' => 'Prozesse löschen',
            'description' => 'Prozesse löschen',
            'ident' => 'processes.delete',
            'active' => true
        ]);

        $p6 = Permission::create([
            'name' => 'Prozess-Versionen fertigstellen',
            'description' => 'Prozess-Versionen fertigstellen',
            'ident' => 'process_versions.create',
            'active' => true
        ]);

        $p7 = Permission::create([
            'name' => 'Prozess-Versionen exportieren',
            'description' => 'Prozess-Versionen exportieren',
            'ident' => 'process_versions.export',
            'active' => true
        ]);

        $p8 = Permission::create([
            'name' => 'Lösungen erstellen & bearbeiten',
            'description' => 'Lösungen erstellen & bearbeiten',
            'ident' => 'solutions.create',
            'active' => true
        ]);

        $p9 = Permission::create([
            'name' => 'Lösungen einsehen',
            'description' => 'Lösungen einsehen',
            'ident' => 'solutions.view',
            'active' => true
        ]);

        $p10 = Permission::create([
            'name' => 'Lösungen löschen',
            'description' => 'Lösungen löschen',
            'ident' => 'solutions.delete',
            'active' => true
        ]);

        $p11 = Permission::create([
            'name' => 'Lösungs-Versionen fertigstellen',
            'description' => 'Lösungs-Versionen fertigstellen',
            'ident' => 'solution_versions.create',
            'active' => true
        ]);

        $p12 = Permission::create([
            'name' => 'Lösungs-Versionen exportieren',
            'description' => 'Lösungs-Versionen exportieren',
            'ident' => 'solution_versions.export',
            'active' => true
        ]);

        $p13 = Permission::create([
            'name' => 'Mitglieder verwalten',
            'description' => 'Mitglieder verwalten',
            'ident' => 'members.manage',
            'active' => true
        ]);

        $p14 = Permission::create([
            'name' => 'Mitglieder einsehen',
            'description' => 'Mitglieder einsehen',
            'ident' => 'members.view',
            'active' => true
        ]);

        $p15 = Permission::create([
            'name' => 'Lizenzen einsehen',
            'description' => 'Lizenzen einsehen',
            'ident' => 'licenses.view',
            'active' => true
        ]);

        $p16 = Permission::create([
            'name' => 'Lizenzen verwalten',
            'description' => 'Lizenzen verwalten',
            'ident' => 'licenses.manage',
            'active' => true
        ]);

        $p17 = Permission::create([
            'name' => 'Plattformen verwalten',
            'description' => 'Plattformen verwalten',
            'ident' => 'platforms.manage',
            'active' => true
        ]);

        $p18 = Permission::create([
            'name' => 'Organisation verwalten',
            'description' => 'Organisation verwalten',
            'ident' => 'organisation.manage',
            'active' => true
        ]);

        // Administrator
        $adminRole = Role::create([
            'name' => 'Administrator',
            'description' => 'Vollständigen Zugriff auf alle Bereiche der Organisation.',
            'is_admin' => true
        ]);

        $adminRole->addPermission($p1);
        $adminRole->addPermission($p2);
        $adminRole->addPermission($p3);
        $adminRole->addPermission($p4);
        $adminRole->addPermission($p5);
        $adminRole->addPermission($p6);
        $adminRole->addPermission($p7);
        $adminRole->addPermission($p8);
        $adminRole->addPermission($p9);
        $adminRole->addPermission($p10);
        $adminRole->addPermission($p11);
        $adminRole->addPermission($p12);
        $adminRole->addPermission($p13);
        $adminRole->addPermission($p14);
        $adminRole->addPermission($p15);
        $adminRole->addPermission($p16);
        $adminRole->addPermission($p17);
        $adminRole->addPermission($p18);

        // Entwickler
        $developerRole = Role::create([
            'name' => 'Prozess-Entwickler',
            'description' => 'Prozesse & Lösungen entwickeln & exportieren.',
        ]);

        $developerRole->addPermission($p1);
        $developerRole->addPermission($p2);
        $developerRole->addPermission($p3);
        $developerRole->addPermission($p4);
        $developerRole->addPermission($p6);
        $developerRole->addPermission($p7);
        $developerRole->addPermission($p8);
        $developerRole->addPermission($p9);
        $developerRole->addPermission($p11);
        $developerRole->addPermission($p12);
        $developerRole->addPermission($p14);
        $developerRole->addPermission($p15);

        $organisation->addRole($adminRole);
        $organisation->addRole($developerRole);

    }
}
