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
    public function up(): void {
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            foreach ($versions as $version) {
                $calculated = $version->calculated;

                foreach ($version->calculated as $key => $item) {
                    if (array_key_exists('classes', $item) && str_contains($item['classes'], 'edge') && !str_contains($item['classes'], 'straight')) {
                        $item['classes'] .= ' straight';
                        $calculated[$key] = $item;
                    }
                }
                $version->update(['calculated' => $calculated]);
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $versions) {
            foreach ($versions as $version) {
                $calculated = $version->calculated;

                foreach ($version->calculated as $key => $item) {
                    if (array_key_exists('classes', $item) && str_contains($item['classes'], 'edge') && !str_contains($item['classes'], 'straight')) {
                        $item['classes'] .= ' straight';
                        $calculated[$key] = $item;
                    }
                }
                $version->update(['calculated' => $calculated]);
            }
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void {
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            foreach ($versions as $version) {
                $calculated = $version->calculated;

                foreach ($version->calculated as $key => $item) {
                    if (array_key_exists('classes', $item) && str_contains($item['classes'], 'edge')) {
                        $item['classes'] = str_replace(' straight', '', $item['classes']);
                        $calculated[$key] = $item;
                    }
                }
                $version->update(['calculated' => $calculated]);
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $versions) {
            foreach ($versions as $version) {
                $calculated = $version->calculated;

                foreach ($version->calculated as $key => $item) {
                    if (array_key_exists('classes', $item) && str_contains($item['classes'], 'edge')) {
                        $item['classes'] = str_replace(' straight', '', $item['classes']);
                        $calculated[$key] = $item;
                    }
                }
                $version->update(['calculated' => $calculated]);
            }
        });
    }
};
