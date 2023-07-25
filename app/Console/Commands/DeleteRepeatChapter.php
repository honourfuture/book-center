<?php

namespace App\Console\Commands;

use App\Models\Article;
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


        $article = Article::with(['chapters']);
        if ($article_id) {
            $article = $article->where('articleid', $article_id);
        }
        $article->orderBy('chapterorder', 'desc')
            ->chunk(50, function ($chapters) {
                $repeats = $has_chapters = [];
                foreach ($chapters as $chapter) {
                    $chapter_name = $chapter->chaptername;
                    if (in_array($chapter_name, $has_chapters)) {
                        $repeats[] = $chapter->toArray();
                        continue;
                    }
                    $has_chapters[] = $chapter_name;
                }

                print_r($repeats);
            });

    }

}
