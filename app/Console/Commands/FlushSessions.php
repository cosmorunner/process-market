<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Flush all users sessions.
 * Class FlushSessions
 * @package App\Console\Commands
 */
class FlushSessions extends Command {

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:session_flush';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Flush all users sessions';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle() : int {
        // Delete all records from the sessions table.
        $table = config('session.table');

        if (Schema::hasTable($table)) {
            DB::table($table)->truncate();
            $this->info('Database sessions deleted.');
        }

        // Delete all sessions related files.
        $path = config('session.files');

        if (File::exists($path)) {
            $files = File::allFiles($path);
            File::delete($files);
            $this->info('File sessions deleted.');
        }
        return 0;
    }
}
