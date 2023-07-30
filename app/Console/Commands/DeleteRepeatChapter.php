<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Chapter;
use App\Services\SitemapService;
use Illuminate\Console\Command;

class DeleteRepeatChapter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:repeatChapter {--article_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '删除重复章节';

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
        $article_id = $this->option('article_id');

        $repeat_article_ids = [];

        $article = Article::select(['articleid', 'articlename', 'lastchapterid', 'lastchapter', 'chapters as total_chapters'])->with(['chapters' => function ($query) {
            $query->orderBy('chapterorder', 'asc');
        }]);
        if ($article_id) {
            $article = $article->where('articleid', $article_id);
        }
        $article
            ->chunk(10, function ($articles) use($repeat_article_ids) {

                foreach ($articles as $article) {
                    logger('check', [$article->articleid]);
                    $repeats = $has_chapters = [];
                    foreach ($article->chapters as $chapter) {

                        $chapter_name = $chapter->chaptername;
                        if (in_array($chapter_name, $has_chapters)) {
                            $repeats[] = $chapter->toArray();
                            continue;
                        }

                        $has_chapters[] = $chapter_name;

                        if($repeats && count($repeats )> 3){
                            $repeat_article_ids[] = $article->articleid;
                            logger('repeats', [$article->articleid]);
                            logger('repeat chapter name', $repeats);
                            break;
                        }
                    }


                }
            });
        logger('all_repeats', $repeat_article_ids);
    }

}
