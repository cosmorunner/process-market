<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
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
                $definition = $version->getRawDefintion();
                $roles = $definition['roles'];
                foreach ($roles as $roleKey => $role) {
                    foreach ($role['permissions'] as $permissionKey => $permission) {
                        $roles[$roleKey]['permissions'][$permissionKey]['conditions'] = [];
                    }
                }

                $definition['roles'] = $roles;
                $version->update(['definition' => $definition]);
                $version->exportDefinition();
                $version->exportDependencies();

                // Export latest version
                if ($version->process->latest_published_version_id === $version->id) {
                    $version->exportDefinition('latest');
                    $version->exportDependencies('latest');
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $roles = $definition['roles'];
                foreach ($roles as $roleKey => $role) {
                    foreach ($role['permissions'] as $permissionKey => $permission) {
                        $roles[$roleKey]['permissions'][$permissionKey]['conditions'] = [];
                    }
                }

                $definition['roles'] = $roles;
                $history->update(['definition' => $definition]);
            }
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->getRawDefintion();
                $roles = $definition['roles'];
                foreach ($roles as $roleKey => $role) {
                    foreach ($role['permissions'] as $permissionKey => $permission) {
                        unset($roles[$roleKey]['permissions'][$permissionKey]['conditions']);
                    }
                }

                $definition['roles'] = $roles;
                $version->update(['definition' => $definition]);

                $version->exportDefinition();
                $version->exportDefinition('latest');
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;
                $roles = $definition['roles'];
                foreach ($roles as $roleKey => $role) {
                    foreach ($role['permissions'] as $permissionKey => $permission) {
                        unset($roles[$roleKey]['permissions'][$permissionKey]['conditions']);
                    }
                }

                $definition['roles'] = $roles;
                $history->update(['definition' => $definition]);
            }
        });
    }
};
