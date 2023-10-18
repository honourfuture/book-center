<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ArticleVisit;
use Illuminate\Console\Command;

class InitArticleVisit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:article';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'init:article';

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

        ArticleVisit::where('article_id', '>', 0)->delete();
        Article::select(['articleid', 'sortid'])->chunk(2000, function ($articles){
            $article_visits = [];
            foreach ($articles as $article){
                $article_visits[] = [
                    'article_id' => $article->articleid,
                    'sort_id' => $article->sortid,
                ];
            }
            ArticleVisit::insert($article_visits);
        });
    }

}
