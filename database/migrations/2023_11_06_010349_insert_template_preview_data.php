<?php

use App\Enums\Settings;
use Illuminate\Database\Migrations\Migration;
use App\Models\ProcessVersion;
use App\Models\Setting;
use App\ProcessType\Template;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * Insert default preview data datasets for each template.
 */
return new class extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;

                $datasets = $definition->templates->mapWithKeys(fn(Template $template) => [$template->id => [$template->defaultPreviewDataset()]])
                    ->toArray();

                Setting::insertSetting(Settings::TemplatePreviewDatasets->value, $datasets, $version);
            }
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Setting::whereName(Settings::TemplatePreviewDatasets->value)->delete();
        Artisan::call('redis:flushdb');
    }
};
