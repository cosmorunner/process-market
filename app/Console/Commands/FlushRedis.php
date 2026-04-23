<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

/**
 * Flushed die Redis Datenbank.
 * Class FlushRedis
 * @package App\Console\Commands
 */
class FlushRedis extends Command {

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'redis:flushdb';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Leert die Redis Datenbank.';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle() {
        try {
            Redis::flushdb();
            $this->info('Die Redis DB wurde geleert.');

            return 0;
        }
        catch (Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
