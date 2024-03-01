<?php

namespace App\Console\Commands;

use App\Enums\QueueNameEnum;
use App\Jobs\AutoArticle00ShuJob;
use App\Jobs\AutoArticleMayiJob;
use App\Jobs\AutoArticleTTJob;
use App\Jobs\AutoArticle69ShuJob;

use App\Jobs\AutoArticleXWBiQuGeJob;
use App\Jobs\AutoFixArticleMayiJob;
use Illuminate\Console\Command;

class PushAutoFixChapterNameJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:chapter {--article_ids=} {--site=} {--limit=}';

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
        $limit = $this->option('limit');

        if(!$limit){
            $limit = 0;
        }
        if(!$site){
            $site = 'mayi';
        }

        foreach ($article_ids as $article_id) {
            switch ($site) {
                case 'mayi':
                    dispatch((new AutoFixArticleMayiJob($article_id, $site, $limit))->onQueue(QueueNameEnum::UPDATE_CHAPTER_MAYI_JOB));
                    break;
            }
        }
    }
}
