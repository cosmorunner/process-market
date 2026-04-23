<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use App\ProcessType\Permission;
use App\ProcessType\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::query()->with('process')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;
                $roles = $definition->roles;
                /* @var Role $role */
                foreach ($roles as $role) {
                    $role->permissions->add(new Permission([
                        'id' => '17f266f4-4970-4ea6-bdb4-327c63fed49d',
                        'name' => 'Alle Aktions-Ausführungen einsehen',
                        'description' => 'Alle Aktions-Ausführungen einsehen.',
                        'ident' => 'process_type.process.view_action_executions',
                        'conditions' => []
                    ]));
                }
                $version->update(['definition' => $definition->toArray()]);
                $version->exportDefinition();

                // Export latest version
                if ($version->process->latest_published_version_id === $version->id) {
                    $version->exportDefinition('latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $roles = $definition->roles;

                foreach ($roles as $role) {
                    $role->permissions->add(new Permission([
                        'id' => '17f266f4-4970-4ea6-bdb4-327c63fed49d',
                        'name' => 'Alle Aktions-Ausführungen einsehen',
                        'description' => 'Alle Aktions-Ausführungen einsehen.',
                        'ident' => 'process_type.process.view_action_executions',
                        'conditions' => []
                    ]));
                }
                $definition->roles = $roles;
                $history->update(['definition' => $definition->toArray()]);
            }
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        ProcessVersion::query()->with('process')->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;
                $roles = $definition->roles;
                /* @var Role $role */
                foreach ($roles as $role) {
                    $role->permissions = $role->permissions->reject(function (Permission $permission) {
                        return $permission->ident == 'process_type.process.view_action_executions';
                    });
                }
                $version->update(['definition' => $definition->toArray()]);
                $version->exportDefinition();

                // Export latest version
                if ($version->process->latest_published_version_id === $version->id) {
                    $version->exportDefinition('latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $roles = $definition->roles;

                foreach ($roles as $role) {
                    $role->permissions = $role->permissions->reject(function (Permission $permission) {
                        return $permission->ident == 'process_type.process.view_action_executions';
                    });
                }
                $definition->roles = $roles;
                $history->update(['definition' => $definition->toArray()]);
            }
        });
    }
};
