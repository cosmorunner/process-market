<?php

use App\Models\Environment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        Environment::query()->chunk(10, function (Collection $environments) {
            /* @var Environment $environment */
            foreach ($environments as $environment) {
                $blueprint = $environment->getRawBlueprint();
                $groups = $blueprint['groups'] ?? [];

                foreach ($groups as $index => $group) {
                    $groups[$index]['identity_process_type'] = '';
                }

                $blueprint['groups'] = $groups;
                $environment->update(['blueprint' => $blueprint]);
            }
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Environment::query()->chunk(10, function (Collection $environments) {
            /* @var Environment $environment */
            foreach ($environments as $environment) {
                $blueprint = $environment->getRawBlueprint();
                $groups = $blueprint['groups'] ?? [];

                foreach ($groups as $index => $group) {
                    unset($groups[$index]['identity_process_type']);
                }

                $blueprint['groups'] = $groups;
                $environment->update(['blueprint' => $blueprint]);
            }
        });
    }
};
