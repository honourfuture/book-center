<?php

namespace App\Console\Commands;

use App\Enums\RuleEnum;
use App\Models\OriginArticleId;
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

        $update_ids = [];

        foreach ($all_sqlite as $sqlite) {
            $taskLogs = DB::connection($sqlite)->table('taskLog')
                ->groupBy(['RULEFILE', 'NID'])
                ->get()
                ->toArray();

            foreach ($taskLogs as $log) {
                if(!$log->NID || !$log->GETID){
                    continue;
                }

                if (in_array($log->RULEFILE, RuleEnum::MA_YI)) {
                    $update_ids[$log->NID]['mayi'] = $log->GETID;
                }

                if (in_array($log->RULEFILE, RuleEnum::TT)) {
                    $update_ids[$log->NID]['tt'] = $log->GETID;
                }

                if (in_array($log->RULEFILE, RuleEnum::LJ_SHU)) {
                    $update_ids[$log->NID]['69shu'] = $log->GETID;
                }
            }
        }

        foreach ($update_ids as $article_id => $update){
            OriginArticleId::updateOrCreate([
                'article_id' => $article_id,
            ], $update);
        }
    }

}
