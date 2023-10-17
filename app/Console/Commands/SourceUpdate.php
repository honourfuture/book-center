<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\NginxAccessLog;
use App\Models\SourceArticle;
use Diwms\NginxLogAnalyzer\Parse;
use Diwms\NginxLogAnalyzer\NginxAccessLogFormat;
use Diwms\NginxLogAnalyzer\RegexPattern;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SourceUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'source:update {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'source:update';

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
        $type = $this->option('type');
        if(!$type || !in_array($type, ['all', 'append'])){
            $this->error("type值 [all] [append]");
        }

        $article_model = Article::select(['articleid', 'articlename', 'author'])->orderBy('articleid', 'asc');
        if($type == 'append'){
            $max_id = SourceArticle::select([DB::raw('max(local_article_id) as id')])->first();
            $article_model = $article_model->where('local_article_id', '>', $max_id);
        }

        $article_model->chunk(500, function ($artciles){
            foreach ($artciles as $artcile){
                $this->info("{$artcile->articleid} {$artcile->articlename}");
                SourceArticle::where('article_name', $artcile->articlename)->where('author', $artcile->author)->update([
                    'local_article_id' => $artcile->articleid
                ]);
            }
        });
        die;

        $source_articles = SourceArticle::select('*')->whereNull('article_id')->chunk(1000, function ($source_articles) {

            foreach ($source_articles as $article) {
                $pattern = '/","copyright":".*/';
                $article_name = preg_replace($pattern, '', $article->article_name);
                $article_name = trim($article_name);
                $pattern = '/https:\/\/www\.ttshuba\.org\/info-(\d+)\.html/';

                preg_match($pattern, $article->origin_url, $matches);

                $id = $matches[1];

                SourceArticle::where('id', $article->id)->update([
                    'article_name' => $article_name,
                    'article_id' => $id,
                    'source' => 'tt'
                ]);
                $this->info($article_name);
            }
        });
    }

}
