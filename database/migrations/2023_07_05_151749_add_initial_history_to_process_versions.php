<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        $processVersions = ProcessVersion::whereNull('published_at')->get();

        /* @var ProcessVersion $processVersion */
        foreach ($processVersions as $processVersion) {
            $history = ProcessVersionHistory::make([
                'command' => null,
                'command_payload' => null,
                'calculated' => $processVersion->calculated,
                'definition' => $processVersion->definition->toArray()
            ]);
            $processVersion->history()->save($history);
            $processVersion->update([
                'history_head' => $history->id
            ]);
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        ProcessVersionHistory::truncate();
        ProcessVersion::whereNull('published_at')->update([
            'history_head' => null
        ]);
    }
};
