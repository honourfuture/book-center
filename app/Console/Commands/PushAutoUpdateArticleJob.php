<?php

namespace App\Console\Commands;

use App\Enums\QueueNameEnum;
use App\Jobs\AutoArticleMayiJob;
use App\Jobs\AutoArticleTTJob;

use Illuminate\Console\Command;

class PushAutoUpdateArticleJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:article {--article_ids=} {--site=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '推送自动更新id到队列';

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
        $article_ids = $this->option('article_ids');

        $article_ids = explode(',', $article_ids);

        $site = $this->option('site');

        if(!$site){
            $site = 'mayi';
        }

        foreach ($article_ids as $article_id) {
            switch ($site) {
                case 'mayi':
                    dispatch((new AutoArticleMayiJob($article_id, $site))->onQueue(QueueNameEnum::UPDATE_MAYI_JOB));
                    break;
                case 'tt':
                    dispatch((new AutoArticleTTJob($article_id, $site))->onQueue(QueueNameEnum::UPDATE_TT_JOB));
                    break;
            }
        }
    }
}