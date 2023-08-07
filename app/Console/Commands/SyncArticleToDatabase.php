<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncArticleToDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:article';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步sqlite中的articleid到source_articles中';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $all_sqlite = config('database.all_sqlite');

        foreach ($all_sqlite as $sqlite) {
            $taskLog = DB::connection($sqlite)->table('taskLog')
                ->groupBy(['RULEFILE'])
                ->get()
                ->toArray();

            print_R($taskLog);die;
        }
    }

}
